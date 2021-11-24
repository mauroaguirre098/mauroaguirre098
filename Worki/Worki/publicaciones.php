<?php include("template/cabecera.php"); ?>
<?php include("administrador/config/db.php"); 
$sentenciaSQL= $conexion->prepare("SELECT * FROM post");
$sentenciaSQL->execute();
$listaPost=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach($listaPost as $Post){?>

<div class="col-md-3">
<div class="card">
<img class="card-img-top" src="./img/<?php echo $Post['imagen']; ?>" alt="">
<div class="card-body">
    <h4 class="card-title"><?php echo $Post['nombre']; ?></h4>
    <a name="" id="" class="btn btn-primary" href="#" role="button">Ver mas</a>
</div>
</div>
</div>

<?php }?>



<?php include("template/pie.php"); ?>