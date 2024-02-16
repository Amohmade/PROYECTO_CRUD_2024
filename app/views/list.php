
<form>
<button type="submit" name="orden" value="Nuevo"> Cliente Nuevo </button><br>
</form>
<br>
<form method="get">
<table>
<tr>
    
    <th>id <br><input type="submit" name="id" value="↑"><input type="submit" name="id" value="↓"></th>
    <th>first_name <br><input type="submit" name="first_name" value="↑"><input type="submit" name="first_name" value="↓"></th>
    <th>email <br><input type="submit" name="email" value="↑"><input type="submit" name="email" value="↓"></th>
    <th>gender <br><input type="submit" name="gender" value="↑"><input type="submit" name="gender" value="↓"></th>
    <th>ip_address <br><input type="submit" name="ip_address" value="↑"><input type="submit" name="ip_address" value="↓"></th>
    <th>teléfono</th>
</tr>
<?php foreach ($tvalores as $valor): ?>
<tr>
<td><?= $valor->id ?> </td>
<td><?= $valor->first_name ?> </td>
<td><?= $valor->email ?> </td>
<td><?= $valor->gender ?> </td>
<td><?= $valor->ip_address ?> </td>
<td><?= $valor->telefono ?> </td>
<td><a href="#" onclick="confirmarBorrar('<?=$valor->first_name?>',<?=$valor->id?>);" >Borrar</a></td>
<td><a href="?orden=Modificar&id=<?=$valor->id?>">Modificar</a></td>
<td><a href="?orden=Detalles&id=<?=$valor->id?>" >Detalles</a></td>

<tr>
<?php endforeach ?>
</table>
</form>
<form>
<br>
<button type="submit" name="nav" value="Primero"> << </button>
<button type="submit" name="nav" value="Anterior"> < </button>
<button type="submit" name="nav" value="Siguiente"> > </button>
<button type="submit" name="nav" value="Ultimo"> >> </button>
</form>