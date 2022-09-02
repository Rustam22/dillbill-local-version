importScripts("https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js")
importScripts("https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js")

const firebaseConfig = {
    apiKey: "AIzaSyBt5HbqCNFFxbEPy88LRD4oLMWmw_dLkSY",
    authDomain: "fcm-web-dillbill.firebaseapp.com",
    projectId: "fcm-web-dillbill",
    storageBucket: "fcm-web-dillbill.appspot.com",
    messagingSenderId: "1031148163457",
    appId: "1:1031148163457:web:73f2ae24803f557a1b1880"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    /*// Customize notification here
    const notificationTitle = 'Background Message Title';
    const notificationOptions = {
        body: 'Background Message Text.',
        icon: 'https://dillbill.com/img/favicon.ico',
        click_action: "https://dillbill.com"
    };

    self.registration.showNotification(notificationTitle, notificationOptions);*/
});