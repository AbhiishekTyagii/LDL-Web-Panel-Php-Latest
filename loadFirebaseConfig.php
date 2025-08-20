<?php
header("Content-Type: application/javascript");
echo "const firebaseConfig = {
  apiKey: '" . getenv("AIzaSyA6OFn-96R1IYOcqvzoqq_Sgl_0AQBtRiY") . "',
  authDomain: 'ldlwebapp.firebaseapp.com',
  databaseURL: 'https://ldlwebapp-default-rtdb.firebaseio.com',
  projectId: 'ldlwebapp',
  storageBucket: 'ldlwebapp.appspot.com',
  messagingSenderId: '992014256637',
  appId: '1:992014256637:web:2fd6be59c1520cc2133194'
};";
?>