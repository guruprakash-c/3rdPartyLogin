import fetch from 'node-fetch';

const WHATSAPP_TOKEN = 'YOUR_WHATSAPP_ACCESS_TOKEN';
const WHATSAPP_PHONE_NUMBER_ID = 'YOUR_WHATSAPP_PHONE_NUMBER_ID';

async function sendWhatsAppMessage(to, message) {
  const url = `https://graph.facebook.com/v13.0/${WHATSAPP_PHONE_NUMBER_ID}/messages`;
  
  const data = {
    messaging_product: "whatsapp",
    to: to,
    type: "text",
    text: { body: message }
  };

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${WHATSAPP_TOKEN}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });

    if (response.ok) {
      console.log('Message sent successfully');
      return await response.json();
    } else {
      console.error('Failed to send message:', await response.text());
      return null;
    }
  } catch (error) {
    console.error('Error sending message:', error);
    return null;
  }
}

// Example usage
sendWhatsAppMessage('1234567890', 'Hello from Node.js!').then(result => {
  console.log('Result:', result);
});