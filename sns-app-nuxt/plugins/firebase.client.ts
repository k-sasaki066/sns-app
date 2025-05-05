import { initializeApp } from "firebase/app"
import { getAuth } from "firebase/auth"
import { getAnalytics } from "firebase/analytics";

const firebaseConfig = {
    apiKey: "AIzaSyA8UIMsMy6xpjB0731QizfffEJvt_3veFw",
    authDomain: "sns-app-41e20.firebaseapp.com",
    projectId: "sns-app-41e20",
    storageBucket: "sns-app-41e20.firebasestorage.app",
    messagingSenderId: "793505736769",
    appId: "1:793505736769:web:80f4c64528bbb94c337ec0",
    measurementId: "G-B7XNMHMSBJ"
}

const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const auth = getAuth(app);

export default defineNuxtPlugin(() => {
    return {
        provide: {
            auth
        }
    }
})