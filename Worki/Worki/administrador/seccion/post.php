<?php include('../template/cabecera.php') ?>

<center> 
<h1 class="display-3">Bienvenido digita la informacion del post que deseas crear</h1>
<div style=" width: 1150px; height: 260px; border: 1px black solid; background: url('cielo.jpg') no-repeat; background-size: cover; "></div>
<br/>
<br/>

    <br/>
  <br/>
</center>                
<?php 

$txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
$txtNombre=(isset($_POST['txtNombre']))?$_POST['txtNombre']:"";
$txtImagen=(isset($_FILES['txtImagen']['name']))?$_FILES['txtImagen']['name']:"";
$accion=(isset($_POST['accion']))?$_POST['accion']:"";

include('../config/db.php'); 

switch($accion){

        case "Agregar":
            
            $sentenciaSQL= $conexion->prepare("INSERT INTO post (nombre, imagen) VALUES (:nombre, :imagen);");
            $sentenciaSQL->bindParam(':nombre',$txtNombre);

            $fecha= new DateTime();
            $nombreArchivo = ($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";

            $tmpImagen=$_FILES["txtImagen"]["tmp_name"];

            if($tmpImagen!= ""){
                move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);
            }
            $sentenciaSQL->bindParam(':imagen',$nombreArchivo); 
            $sentenciaSQL->execute();
            header("Location:post.php");

            break;

            

        case "Modificar":

            $sentenciaSQL= $conexion->prepare("UPDATE post SET nombre=:nombre WHERE id=:id");
            $sentenciaSQL->bindParam(':nombre',$txtNombre);
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
            
            if($txtImagen!=""){

            $fecha= new DateTime();
            $nombreArchivo = ($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";

            $tmpImagen=$_FILES["txtImagen"]["tmp_name"];
            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);

            $sentenciaSQL= $conexion->prepare("SELECT imagen FROM post WHERE id=:id");
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
            $Post=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

            if(isset($Post["imagen"]) && ($Post["imagen"]!="imagen.jpg")){

                if(file_exists("../../img/".$Post["imagen"])){

                    unlink("../../img/".$Post["imagen"]);
                }

            }

            $sentenciaSQL= $conexion->prepare("UPDATE post SET imagen=:imagen WHERE id=:id");
            $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
            
            } 
            header("Location:post.php");
            break;

        case "Cancelar":
            header("Location:post.php");
            break;

        case "Seleccionar":
            $sentenciaSQL= $conexion->prepare("SELECT * FROM post WHERE id=:id");
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
            $Post=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

            $txtNombre=$Post['nombre'];
            $txtImagen=$Post['imagen'];
            break;

        case "Borrar":

            $sentenciaSQL= $conexion->prepare("SELECT imagen FROM post WHERE id=:id");
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
            $Post=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

            if(isset($Post["imagen"]) && ($Post["imagen"]!="imagen.jpg")){

                if(file_exists("../../img/".$Post["imagen"])){

                    unlink("../../img/".$Post["imagen"]);
                }

            }

            $sentenciaSQL= $conexion->prepare("DELETE FROM post WHERE id=:id");
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();

            header("Location:post.php");
            break;

}

$sentenciaSQL= $conexion->prepare("SELECT * FROM post");
$sentenciaSQL->execute();
$listaPost=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>



<div class="col-md-5">

    <div class="card">

        <div class="card-header">
            Datos del Post
        </div>

        <div class="card-body">

        <form method="POST" enctype="multipart/form-data">

    <div class = "form-group">
    <label for="txtID">ID: </label>
    <input type="text" required readonly  class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" placeholder="Ingrese el ID del usuario">
    </div>

    <div class = "form-group">
    <label for="txtNombre">Usuario:</label>
    <input type="text" required class="form-control" value="<?php echo $txtNombre; ?>" name="txtNombre" id="txtNombre" placeholder="Ingrese el nombre del usuario">
    </div>

   

    <div class = "form-group">  
    <label for="txtImagen">Imagen</label> 

</br>

    <?php if($txtImagen!=""){ ?>

    <img src="../../img/<?php echo $txtImagen;?>" width="50" alt="" srcset="">

    <?php } ?>

    <input type="file" class="form-control" name="txtImagen" id="txtImagen" placeholder="Ingrese el nombre del usuario">
    </div>

    <div class="btn-group" role="group" aria-label="">

    <button type="submit" name="accion" <?php echo ($accion=="Seleccionar")?"disabled":"";?>value="Agregar" class="btn btn-success">Agregar</button>
    <button type="submit" name="accion" <?php echo ($accion!="Seleccionar")?"disabled":"";?>value="Modificar" class="btn btn-warning">Modificar</button>
    <button type="submit" name="accion" <?php echo ($accion!="Seleccionar")?"disabled":"";?>value="Cancelar" class="btn btn-info">Cancelar</button> 

    </div>

    </form>

        </div>

    </div>
    
</div>

<div class="col-md-7">

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Imagen</th>
            
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($listaPost as $post){ ?>
        <tr>
            <td><?php echo $post['id']; ?></td>
            <td><?php echo $post['nombre']; ?></td>
            <td>
            
            <img src="../../img/<?php echo $post['imagen']; ?>" width="50" alt="" srcset="">
        
            
        
        
            </td>

            <td>

            <form method="post">

                <input type="hidden" name="txtID" id="txtID" value="<?php echo $post['id']; ?>"/>
                

                <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary"/>
                
                <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>



            </form>
        
            </td>

        </tr>
        <?php }?>
        
    </tbody>
</table>

</div>

<?php include('../template/pie.php') ?>
