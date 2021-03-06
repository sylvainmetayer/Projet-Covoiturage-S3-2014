<div>
	<h1>Liste des personnes</h1>
</div>
<?php
$pdo = new Mypdo ();
$personneManager = new PersonneManager ( $pdo );
$personnes = $personneManager->getAllPersonnes ();
$departementManager = new DepartementManager ( $pdo );

// var_dump ( $listePersonnes );
if ($personnes == null) { // Pas de personnes enregistrées
	?>
<p>
	D&eacute;sol&eacute;, aucune personne n'est enregistr&eacute;e. <br />
	<strong><a href='index.php?page=1'>Ajouter une personne ?</a></strong>
<?php
} else { // Des personnes sont enregistrées
	
	if (empty ( $_GET ['id'] )) {
		?>
<?php echo "Actuellement ".count($personnes)." personnes sont enregistr&eacute;es\n"; ?>
<br /> <br />
<center>
	<table border='solid'>
		<tr>
			<th>Num&eacute;ro</th>
			<th>Nom</th>
			<th>Pr&eacute;nom</th>
		</tr>
	<?php
		foreach ( $personnes as $personne ) {
			?>
		<tr>
			<td><?php echo "<a href='index.php?page=2&id=".$personne->getPerNum()."'>".$personne->getPerNum()."</a>"; ?> 
		</td>
			<td><?php echo "<a href='index.php?page=2&id=".$personne->getPerNum()."'>".$personne->getNomPersonne()."</a>"; ?>
		</td>
			<td><?php echo "<a href='index.php?page=2&id=".$personne->getPerNum()."'>".$personne->getPrenomPersonne()."</a>"; ?>
		</td>
		</tr>
	<?php
		}
		?>
</table>
</center>
<br />
<?php
	} else {
		$id = $_GET ['id'];
		$pdo = new Mypdo ();
		$etudiantManager = new EtudiantManager ( $pdo );
		$salarieManager = new SalarieManager ( $pdo );
		$personne = $personneManager->getPersonneParId ( $id );
		if ($personneManager->isEtudiant ( $id )) {
			$etudiant = $etudiantManager->getEtudiant ( $id );
			$departements = $departementManager->getDetailsDepartement ( $etudiant->getDepNum () );
			$villeManager = new VilleManager ( $pdo );
			$ville = $villeManager->getNomVille ( $departements->getVilNum () );
			?>
<h1> D&eacute;tail sur l'&eacute;tudiant <?php echo $personne->getNomPersonne();?></h1>
<center>
	<table border='solid'>
		<tr>
			<th>Pr&eacute;nom</th>
			<th>Mail</th>
			<th>T&eacute;l&eacute;phone</th>
			<th>D&eacute;partement</th>
			<th>Ville</th>
		</tr>
		<tr>
			<td><?php echo $personne->getNomPersonne(); ?></td>
			<td><?php echo $personne->getPerMail(); ?></td>
			<td><?php echo $personne->getPerTel(); ?></td>
			<td><?php echo $departements->getDepNom(); ?></td>
			<td><?php echo $ville->getVilleNom(); ?></td>
		</tr>
	</table>
</center>
<?php
		} else if ($personneManager->isSalarie ( $id )) {
			$salarie = $salarieManager->getSalarie ( $id );
			$fonctionManager = new FonctionManager ( $pdo );
			$fonction = $fonctionManager->getDetailsFonction ( $salarie->getFonNum () );
			?>
<h1> D&eacute;tail sur le salari&eacute; <?php echo $personne->getNomPersonne();?></h1>
<center>
	<table border='solid'>
		<tr>
			<th>Pr&eacute;nom</th>
			<th>Mail</th>
			<th>T&eacute;l&eacute;phone</th>
			<th>T&eacute;l&eacute;phone professionnel</th>
			<th>Fonction</th>
		</tr>
		<tr>
			<td><?php echo $personne->getNomPersonne(); ?></td>
			<td><?php echo $personne->getPerMail(); ?></td>
			<td><?php echo $personne->getPerTel(); ?></td>
			<td><?php echo $salarie->getSalTelProf(); ?></td>
			<td><?php echo $fonction->getFonLibelle(); ?></td>
		</tr>
	</table>
</center>
<?php
		} else {
			?>
<p>
	<img src="image/erreur.png" class="imagErreur" alt="Erreur" /> Cette
	personne ne fait partie ni des &eacute;tudiants, ni des salari&eacute;s
</p>
<?php
		}
	}
}
?>
