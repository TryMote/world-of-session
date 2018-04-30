<style>
        .regButton {
          text-transform:uppercase; font-size:0.875em; border:none; background:#f45a40; padding:7px 20px; color:#fff; cursor:pointer;margin-right: 140px;
          margin-top:5px;
        }
        .regButton:hover {
            background:grey ;
        }

	

        #window_signin {
            width: 400px;
            height: 200px;
            margin: 40px auto;
            background: #fff;
            border: 1px solid white;
            z-index: 150; display: none;
            position: fixed; left: 0;right: 0;top:0;bottom: 0;

        } 
        .form{
		display: flex;
		align-items: center;
		flex-direction: column;
		margin: auto;
            width: 275px; text-align:center;
        } 

      .radio{
            margin: ;}

             .close{

                cursor: pointer;border: 1px solid white; padding: 3px; background:white;} .close:hover{
                background: black;
		} 
        #gray_signin{
            opacity: 0.8; 
            padding: 15px;
            background-color: rgba(1,1,1,0.75); 
            position: fixed;
             left: 0;right: 0;top:0;bottom: 0; display: none; z-index:100; overflow: auto; } 
       
  </style>
<?php
	if(isset($_SESSION['in'])) { 
		if($_SESSION['in'] == 0) {
	echo "
</center><button onclick=\"show_signin('block')\" class='signin_button'><p class=\"header-basket\">
                  <svg xmlns=\"http://www.w3.org/2000/svg\" x=\"0px\" y=\"0px\"
     width=\"15\" height=\"15\"
     viewBox=\"0 0 24 24\"
     style=\"fill:#ffffff;\">    <path d=\"M 12 0 C 5.4 0 0 5.4 0 12 C 0 18.6 5.4 24 12 24 C 18.6 24 24 18.6 24 12 C 24 5.4 18.6 0 12 0 z M 12 2 C 17.5 2 22 6.5 22 12 C 22 17.5 17.5 22 12 22 C 6.5 22 2 17.5 2 12 C 2 6.5 6.5 2 12 2 z M 8 4 L 12.408203 12 L 8 20 L 11.591797 20 L 16 12 L 11.591797 4 L 8 4 z\"></path></svg>Вход</button>
    <!-- Задний прозрачный фон -->
    <div onclick=\"show_signin('none')\" id=\"gray_signin\"></div>
<div id=\"window_signin\">
    <!-- Картинка крестика -->
    <div class=\"form\">
        <h2>Войти</h2>
 <form action=\"scripts/signin.php\" name=\"f1\" method='POST'>
            <input type=\"text\" placeholder=\"Электронная почта или никнейм\" name=\"login\" class=\"input\">
            <input type=\"password\" placeholder=\"Пароль\" name=\"pass\" class=\"input\">
            <button type=\"submit\" name=\"signin_menu\">Войти</button> 
        </form>
		<a href='http://localhost/wos/register.php'>Регистрация</a>
    </div>
</div>
 <script>
//Функция показа
    function show_signin(state)
    {
    document.getElementById('window_signin').style.display = state;    
    document.getElementById('gray_signin').style.display = state;      
    }   
</script>";
		} else {
			$profile = get_first_select_array($conn, "SELECT nickname, profile_link FROM sign_in WHERE user_id='".$_SESSION['user_id']."'", MYSQLI_NUM);
			$profile_location = 'http://localhost/wos/users/'.$profile[1];
			echo "<div class='signed'>
			<a href='$profile_location'>$profile[0]</a>
			<form action='http://localhost/wos/index.php' method='POST'>
			<input type='submit' name='exit_profile' value='Выйти'>
			</form>";
		}
	}
?> 
