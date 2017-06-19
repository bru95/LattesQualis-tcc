<!DOCTYPE html>
<style>
    body, html {
        height: 100%;
        background-repeat: no-repeat;
        background-image: linear-gradient(rgb(173,216,230), rgb(119,136,153));
    }

    .container-form{
        position:absolute;
        left:50%;
        top:50%;
        margin-left:-110px;
        margin-top:-40px;
        background-color: #F5FFFA;
        border-radius: 10px;
    }
    
    .input-group{
        margin-bottom: 10px;
        margin-top: 10px;
        margin-left: 40px;
        margin-right: 40px;
    }
    
    #buttonLogin{
        margin-bottom: 20px;
        margin-top: 20px;
        text-align: center;
    }
</style>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TCC Bruna</title>
        <link href="bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="container-form">
                <form method="post" action="apresentacao/login.php" id="formLogin" name="formLogin">
                    <div class="input-group">
                        <label for="login">Login:</label>
                        <input type="text" class="form-control" placeholder="login" aria-describedby="sizing-addon2" name="login" id="login">
                    </div>
                    <div class="input-group">
                        <label for="senha">Senha:</label>
                        <input type="password" class="form-control" placeholder="siape" aria-describedby="sizing-addon2" name="senha" id="senha">
                    </div>
                    <div id="buttonLogin">
                    <button type="submit" class="btn btn-default">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
<script>
</script>
