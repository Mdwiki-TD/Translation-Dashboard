<div class='card-header'>
    <h4>Fix wikirefs options:</h4>
</div>
<div class='card-body'>
<?PHP
//---
echo '
<form action="coordinator.php?ty=wikirefs_options" method="POST">
    <input name="ty" value="wikirefs_options" hidden/>
';


echo "<div>" . var_dump($_POST);
//---
echo '
    <button type="submit" class="btn btn-success">Submit</button>
</form>
';
//---
?>