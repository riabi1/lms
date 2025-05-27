import Echo from "laravel-echo";

window.Echo = new Echo({
    broadcaster: "reverb",
    key: process.env.MIX_REVERB_APP_KEY,
    wsHost: process.env.MIX_REVERB_HOST,
    wsPort: process.env.MIX_REVERB_PORT,
    wssPort: process.env.MIX_REVERB_PORT,
    scheme: process.env.MIX_REVERB_SCHEME,
    authEndpoint: "/broadcasting/auth",
    disableStats: true,
});
