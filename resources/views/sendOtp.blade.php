<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js"></script>

<script>
  // Initialize Firebase
  firebase.initializeApp({{$firebaseAuth}});
  const auth = firebase.auth();

  // Setting up reCAPTCHA verifier (required for web apps)
  window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
    'size': 'invisible',
    'callback': (response) => {
      console.log('reCAPTCHA solved:', response);
    }
  });

  // Function to send OTP
 const appVerifier = window.recaptchaVerifier;
       auth.signInWithPhoneNumber({{$phone}}, appVerifier)
           .then((confirmationResult) => {
            window.confirmationResult = confirmationResult;
            alert('OTP sent successfullyss!');
      })
      .catch((error) => {
        console.error(error);
      });
  
</script>