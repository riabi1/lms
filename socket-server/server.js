const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http, {
    cors: {
        origin: ['http://localhost:8000', 'http://127.0.0.1:8000'],
        methods: ['GET', 'POST'],
        credentials: true
    }
});

app.use(express.json());

io.on('connection', (socket) => {
    console.log('Client connected:', socket.id);

    socket.on('joinConversation', (conversationId, userId, userType) => {
        socket.join(`conversation_${conversationId}`);
        console.log(`${userType} ${userId} joined conversation ${conversationId}`);
    });

    socket.on('disconnect', () => {
        console.log('Client disconnected:', socket.id);
    });
});

app.post('/send-message', (req, res) => {
    const data = req.body;
    console.log('Message received via HTTP:', data);
    io.to(`conversation_${data.conversationId}`).emit('message', data);
    res.status(200).send({ status: 'success' });
});

app.post("/send-notification", (req, res) => {
    const { recipient_id, recipient_type, notification } = req.body;
    console.log(
        `Notification received for ${recipient_type} ${recipient_id}:`,
        notification
    );
    io.to(`${recipient_type}_${recipient_id}`).emit(
        "notification",
        notification
    );
    res.status(200).send({ status: "success" });
});

http.listen(3000, () => {
    console.log('Socket.IO server running on port 3000');
});
