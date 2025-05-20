const express = require('express');
const app = express();
const PORT = 3000;

// Middleware to allow CORS
const cors = require('cors');
app.use(cors());

// Sample endpoint
app.get('/api/data', (req, res) => {
    res.json({ message: 'Hello from the local server!' });
});

// Start the server
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
