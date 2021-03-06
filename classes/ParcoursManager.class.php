<?php
class ParcoursManager {
	private $db;
	public function __construct($db) {
		$this->db = $db;
	}
	public function VerifParcours($vil_num1, $vil_num2) {
		// On selectionne tous les parcours ayant pour départ vil_num1 et arrivée vil_num2
		$sql = "SELECT par_num, par_km, vil_num1, vil_num2 FROM parcours WHERE vil_num1=:vil_num1 AND vil_num2=:vil_num2";
		$requete = $this->db->prepare ( $sql );
		$requete->bindValue ( ":vil_num1", $vil_num1 );
		$requete->bindValue ( ":vil_num2", $vil_num2 );
		
		$requete->execute ();
		
		$resultat = $requete->fetch ( PDO::FETCH_OBJ );
		
		if ($resultat != null) // Le parcours existe déjà
{
			return new Parcours ( $resultat );
			// Il s'agit d'un objet Parcours
		} else {
			return null;
			// Le parcours n'a pas été trouvé, il n'existe donc pas
		}
	}
	public function add($parcours) {
		// on regarde le premier sens
		$sens1 = $this->VerifParcours ( $parcours->getVil_num1 (), $parcours->getVil_num2 () );
		// on regarde l'autre sens
		$sens2 = $this->VerifParcours ( $parcours->getVil_num2 (), $parcours->getVil_num1 () );
		// var_dump($sens1); var_dump($sens2);
		// si $sens1 OU $sens2 sont different de null, ça veut dire que le parcours existe déjà, et qu'il ne faut pas l'ajouter à nouveau
		if ($sens1 != null or $sens2 != null) {
			return null;
			// on quitte sans ajouter de parcours
		}
		
		$requete = $this->db->prepare ( 'INSERT INTO parcours (par_km, vil_num1, vil_num2) VALUES (:km, :vil_num1, :vil_num2);' );
		// var_dump($parcours->getParKm());
		$requete->bindValue ( ':km', $parcours->getParKm () );
		$requete->bindValue ( ':vil_num1', $parcours->getVil_num1 () );
		$requete->bindValue ( ':vil_num2', $parcours->getVil_num2 () );
		
		$retour = $requete->execute ();
		// var_dump($retour);
		return $retour;
	}
	public function getAllParcours() {
		$listeParcours = array (); // tableau d'objet
		
		$sql = 'SELECT par_num, vil_num1, vil_num2, par_km FROM parcours';
		$requete = $this->db->prepare ( $sql );
		$requete->execute ();
		while ( $nom_vil = $requete->fetch ( PDO::FETCH_OBJ ) ) {
			$listeParcours [] = new parcours ( $nom_vil );
		}
		return $listeParcours;
		$requete->closeCursor ();
	}
	public function getVilleArriveePossible($vil_num1) {
		$listeVilles = array (); // tableau d'objet
		$villeManager = new VilleManager ( $this->db );
		$sql = "SELECT vil_num1 AS vil_num FROM parcours WHERE vil_num2=:vil_num1 UNION SELECT vil_num2 FROM parcours WHERE vil_num1=:vil_num1";
		// on met dans une même colonne les resultat des parcours dans les deux sens afin de savoir les ville "proposables"
		$requete = $this->db->prepare ( $sql );
		$requete->bindValue ( ":vil_num1", $vil_num1 );
		
		$requete->execute ();
		
		while ( $ligne = $requete->fetch ( PDO::FETCH_ASSOC ) ) {
			// fetch assoc, car fetch obj marche pas, et fait une erreur.
			$listeVilles [] = $villeManager->getVilleParId ( $ligne ["vil_num"] );
		}
		
		return $listeVilles;
	}
	public function getParcoursParId($idParcours) {
		$sql = "SELECT par_num, par_km, vil_num1, vil_num2 FROM parcours WHERE par_num=:par_num";
		$requete = $this->db->prepare ( $sql );
		$requete->bindValue ( ':par_num', $idParcours );
		
		$requete->execute ();
		$resultat = $requete->fetch ( PDO::FETCH_OBJ );
		
		if ($resultat != null) {
			return new Parcours ( $resultat );
			// On retourne un objet Parcours
		} else {
			return null;
		}
		$requete->closeCursor ();
	}
}