<style>
        .regButton {
          text-transform:uppercase; font-size:0.875em; border:none; background:#f45a40; padding:7px 20px; color:#fff; cursor:pointer;margin-right: 140px;
          margin-top:5px; border-radius: 10px;
        }

        .regButton:hover {
            background:grey ;
        }

        #window {
            width: 500px;
            height: 200px;
            margin: 40px auto;
            background: black;
            border: 1px solid gold;
            border-radius: 20px;
            z-index: 150; display: none;
             position: fixed; left: 0;right: 0;top:0;bottom: 0;

        } 
        .form{
            width: 275px; margin: -15px auto 20px auto; text-align:center;
        } 
        .input{
             width: 260px;
             padding: 5px;
             margin-bottom: 10px;
             border-radius: 5px; } 
        .radio{
            margin: ;}

             .close{

                cursor: pointer;border: 1px solid white; padding: 3px; background:white;} .close:hover{
                background: black;} 
        #gray{
            opacity: 0.8; 
            padding: 15px;
            background-color: rgba(1,1,1,0.75); 
            position: fixed;
             left: 0;right: 0;top:0;bottom: 0; display: none; z-index:100; overflow: auto; } 
       
  </style>
</head>

<body>
    
<center><button onclick="show('block')" class="regButton">Сообщение</button>
  </center>
    <!-- Задний прозрачный фон -->
    <div onclick="show('none')" id="gray"></div>
<div id="window">
    <!-- Картинка крестика -->
    <img class="close" src="i/close.png" alt=""  onclick="show('none')">
    <div class="form">
        <h2>Оправить сообщение</h2>
 <form action="index.html" name="f1">
            <input type="email" placeholder="Почта" name="name1" class="input">
            <textarea placeholder="Сообщение" name="name2" class="input">
              <input type="submit" value="Потвердить" name="sab" class="input"> 
        </form>
    </div>
</div>
 <script>
//Функция показа
    function show(state)
    {
    document.getElementById('window').style.display = state;    
    document.getElementById('gray').style.display = state;      
    }   
</script>   
