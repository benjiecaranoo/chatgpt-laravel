<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="chat-container">
        <!-- Chat history -->
        <div id="chat-history" style="height: 500px; overflow-y: auto;">
            <!-- Previous chat messages will appear here -->
        </div>

        <!-- Form to send new message -->
        <div class="chat-input-container">
            <form id="chat-form">
                <input type="text" id="message" placeholder="Type your message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchChatHistory();
            
            document.getElementById('chat-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const messageInput = document.getElementById('message');
                const message = messageInput.value;
                
                const response = await fetch('/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify({ message })
                });
                
                messageInput.value = '';
                await fetchChatHistory();
            });
        });

        async function fetchChatHistory() {
            const response = await fetch('/chat', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
            });

            const data = await response.json();

            if (data.history) {
                const chatHistoryContainer = document.getElementById('chat-history');
                chatHistoryContainer.innerHTML = '';

                // Loop through each chat message in the history
                data.history.forEach(chat => {
                    // Create a new container for each chat exchange
                    const chatItem = document.createElement('div');
                    
                    // Format the timestamp to local date and time
                    const createdDate = new Date(chat.created_at).toLocaleString();
                    
                    // Create the chat message HTML structure
                    chatItem.innerHTML = `
                        <!-- User Message -->
                        <div class="message-wrapper" style="align-items: flex-start;">
                            <div class="message user-message">
                                <p>${chat.message}</p>
                                <small class="message-time">${createdDate}</small>
                            </div>
                        </div>

                        <!-- AI Response -->
                        <div class="message-wrapper ai">
                            <div class="message ai-message">
                                <p>${chat.response}</p>
                                <small class="message-time">${createdDate}</small>
                            </div>
                        </div>
                    `;
                    
                    // Add the chat exchange to the chat history container
                    chatHistoryContainer.appendChild(chatItem);
                });
                
                // Scroll to bottom
                chatHistoryContainer.scrollTop = chatHistoryContainer.scrollHeight;
            }
        }

        // Your existing form submission code remains the same
    </script>

    <style>
        .chat-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        #chat-history {
            height: 500px;
            overflow-y: auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .message-wrapper {
            display: flex;
            margin-bottom: 20px;
        }

        .message-wrapper.ai {
            justify-content: flex-start;
        }

        .message {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 20px;
            margin: 2px 0;
            font-size: 14px;
            box-shadow: 0 2px 4px #0000001a;
        }

        .user-message {
            background: #007bff;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 5px;
        }

        .ai-message {
            background: #e9ecef;
            color: black;
            border-bottom-left-radius: 5px;
        }

        .chat-input-container {
            margin-top: 20px;
        }

        #chat-form {
            display: flex;
            gap: 10px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 25px;
            outline: none;
        }

        button {
            padding: 12px 24px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0056b3;
        }
    </style>
</x-app-layout>

