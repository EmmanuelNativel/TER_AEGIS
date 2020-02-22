<?php

class CSV_model extends CI_Model
{
	/**
	 *	Insert data from csv file into a data base table.
	 *	@param String $table a data base table
	 *	@param String $file a path of csv file
	 *	@return query
	 */
	public function import_csv($table, $file_path, $delimiter=";", $header=TRUE)
	{
		$csv = array();

		foreach(file($file_path) as $row) {
			array_push($csv, preg_split('/'.$delimiter.'/', $row));
		}

		if($header) {
			$sql_header = implode(', ', $csv[0]);
			$start = 1;
		}
		else $start = 0;

		$sql_data = '';
		$nb_row = count($csv);

		for($i=$start; $i<$nb_row; $i++) {
			$sql_row = "('".implode("', '", $csv[$i])."')";
			$sql_row = str_replace("''", 'NULL', $sql_row);	//On remplace les chaines vide par la valeur NULL
			$sql_data = $sql_data.$sql_row;

			if( (($i+1) % count($csv)) != 0) {
				$sql_data = $sql_data.',';
			}
		}

		$this->db->trans_start();
		if (isset($sql_header)) $this->db->query("INSERT INTO public.".$table." (".$sql_header.") VALUES ".$sql_data.";");
		else 										$this->db->query("INSERT INTO public.".$table." VALUES ".$sql_data.";");
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}
