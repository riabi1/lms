import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

document.addEventListener("DOMContentLoaded", () => {
    let reconnectAttempts = 0;
    const maxReconnectAttempts = 5;

    // Debug WebSocket creation
    const originalWebSocket = window.WebSocket;
    window.WebSocket = function (...args) {
        console.log("Creating WebSocket:", {
            args,
            timestamp: new Date().toISOString(),
        });
        return new originalWebSocket(...args);
    };
    window.WebSocket.prototype = originalWebSocket.prototype;

    // Initialize Laravel Echo with Reverb
    try {
        window.Echo = new Echo({
            broadcaster: "reverb",
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            scheme: import.meta.env.VITE_REVERB_SCHEME ?? "http",
            forceTLS:
                (import.meta.env.VITE_REVERB_SCHEME ?? "http") === "https",
            enabledTransports: ["ws"],
            disableStats: true,
            authEndpoint: "/broadcasting/auth",
            auth: {
                headers: {
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "missing",
                    Accept: "application/json",
                    "X-Socket-ID": null,
                },
            },
        });

        console.log("Laravel Echo initialized:", {
            key: import.meta.env.VITE_REVERB_APP_KEY,
            host: import.meta.env.VITE_REVERB_HOST,
            port: import.meta.env.VITE_REVERB_PORT,
            scheme: import.meta.env.VITE_REVERB_SCHEME,
            socketId: window.Echo.socketId?.() || "Not connected",
            timestamp: new Date().toISOString(),
        });

        // Monitor authentication requests
        const originalFetch = window.fetch;
        window.fetch = async function (...args) {
            if (args[0].includes("/broadcasting/auth")) {
                console.log("Broadcasting auth request:", {
                    url: args[0],
                    headers: args[1]?.headers,
                    timestamp: new Date().toISOString(),
                });
            }
            const response = await originalFetch(...args);
            if (args[0].includes("/broadcasting/auth") && !response.ok) {
                console.error("Broadcasting auth failed:", {
                    status: response.status,
                    statusText: response.statusText,
                    timestamp: new Date().toISOString(),
                });
            }
            return response;
        };

        // Check for connection timeout
        setTimeout(() => {
            if (window.Echo.connector.pusher.connection.state !== "connected") {
                console.error("WebSocket connection timeout after 10 seconds", {
                    state: window.Echo.connector.pusher.connection.state,
                    timestamp: new Date().toISOString(),
                });
            }
        }, 10000);
    } catch (error) {
        console.error("Failed to initialize Laravel Echo:", {
            message: error.message,
            stack: error.stack,
            timestamp: new Date().toISOString(),
        });
    }

    // Log Pusher connection events
    window.Echo?.connector.pusher.connection.bind("connected", () => {
        reconnectAttempts = 0;
        console.log("WebSocket connected:", {
            socketId: window.Echo.socketId(),
            host: import.meta.env.VITE_REVERB_HOST,
            port: import.meta.env.VITE_REVERB_PORT,
            scheme: import.meta.env.VITE_REVERB_SCHEME,
            timestamp: new Date().toISOString(),
        });
    });

    window.Echo?.connector.pusher.connection.bind("disconnected", () => {
        console.error("WebSocket disconnected.", {
            reconnectAttempts,
            maxReconnectAttempts,
            timestamp: new Date().toISOString(),
        });
        if (reconnectAttempts < maxReconnectAttempts) {
            reconnectAttempts++;
            console.log(
                `Attempting to reconnect (${reconnectAttempts}/${maxReconnectAttempts}) in 3 seconds...`,
                {
                    timestamp: new Date().toISOString(),
                }
            );
            setTimeout(() => {
                console.log("Initiating WebSocket reconnect...", {
                    timestamp: new Date().toISOString(),
                });
                window.Echo?.connector.pusher.connect();
            }, 3000);
        } else {
            console.error(
                "Max reconnect attempts reached. Please check server status or refresh the page.",
                {
                    timestamp: new Date().toISOString(),
                }
            );
        }
    });

    window.Echo?.connector.pusher.connection.bind("error", (error) => {
        console.error("WebSocket connection error:", {
            message: error?.message || "No message provided",
            type: error?.type || "Unknown",
            code: error?.code || "No code provided",
            details: error || {},
            url: `ws://${import.meta.env.VITE_REVERB_HOST}:${
                import.meta.env.VITE_REVERB_PORT
            }/app/${import.meta.env.VITE_REVERB_APP_KEY}`,
            timestamp: new Date().toISOString(),
        });
    });

    window.Echo?.connector.pusher.connection.bind("state_change", (states) => {
        console.log("Pusher state change:", {
            previous: states.previous,
            current: states.current,
            timestamp: new Date().toISOString(),
        });
    });

    window.Echo?.connector.pusher.connection.bind("event", (data) => {
        console.log("Raw Pusher event received:", {
            data,
            timestamp: new Date().toISOString(),
        });
    });
});
