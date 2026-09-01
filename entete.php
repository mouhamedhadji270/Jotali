<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>JOTALI</title>
        <meta charset="UTF-8">
    </head>
    <body>
    
        <?php if(isset($_SESSION['user'])){ ?>
        <div class="user-box">
            <header>
            <h1>Jotali</h1>
            <navbar>
                <ul>
                    <li><a href="accueil.php"><button>Accueil</button></a></li>
                    <li><a href="deconnexion.php"><button>Deconnexion</button></a></li>
                    <li><a href="./articles/index.php"><button>Editer</button></a></li>
                </ul>
                    <strong><?= htmlspecialchars($_SESSION['user']['login']) ?></strong>

                </div>
                <?php }else{
                ?>
                <header>
                    <h1>Jotali</h1>
                    <navbar>
                    <ul>
                        <li><a href="accueil.php"><button>Accueil</button></a></li>
                        <li><a href="connexion.php"><button>Connexion</button></a></li>
                    </ul>
            </navbar> 
            </header> 
        <?php
        }
        ?> 

    </body>
  
</html>