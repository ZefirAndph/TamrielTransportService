<?php
	define('D', '/');
	class TTS
	{
		private $db;
		public function __construct()
		{
			$this->db = new PDO("sqlite:data.db");
		}
		public function Routes($from, $to, $depth)
		{
			$valid = [];
			foreach($this->BuildRoutes($from, $depth) as $path)
			{
				$e = explode(D, $path);
				if(end($e) == $to)
					$valid[] = $path;
			}
			return $valid;
		}
		public function Route($from, $to)
		{
			$retv = [];
			foreach($this->Routes($from, $to, 10) as $route)
			{
				$t = new stdClass;
				$t->Trasa = $route;
				$t->Useky = [];
				
				foreach($this->SST($route) as $p)
				{
					$temp = new stdClass;
					$temp->name = $p;
					$temp->Info = $this->RInfo($p);
					$temp->Cena = 0;
					$t->Useky[] = $temp;
				}
				$retv[] = $t;
			}
			return $retv;
		}
		public function SST($path) // split to singlke travels
		{
			$retv = [];
			$e = explode(D, $path);
			for($i = 0; $i < count($e)-1; $i++)
				$retv[] = $e[$i] . D . $e[$i+1];
			return $retv;
		}
		public function Ico($type)
		{
			switch($type)
			{
				case "CART": return '<i class="fa-solid fa-trailer"></i>'; break;
				case "LOD": return '<i class="fa-solid fa-ship"></i>'; break;
				default: return '<i class="fa-solid fa-route"></i>'; break;
			}
		}
		public function RInfo($path)
		{
			$q = $this->db->prepare("SELECT * FROM `paths` WHERE `from` = ? AND `to` = ?");
			$d = explode(D, $path);
			$q->execute($d);
			return $q->fetchAll(\PDO::FETCH_OBJ)[0];
		}
		public function BuildRoutes($path, $depth)
		{
			$routes = [$path];

			for($i = 0; $i < $depth; $i++)
			{
				$routes = $this->WaveExpand($routes);
			}
			return $routes;
		}

		private function WaveExpand($retv)
		{
			foreach($retv as $path)
			{
				$e = explode(D, $path);
				foreach($this->Access(end($e)) as $t)
				{
					// už tam je
					if(in_array($path . D . $t, $retv))
						continue;

					//vrací se..
					if(strpos($path, $t) !== false)
						continue;

					$retv[] = $path . D . $t;
				}
			}
			return $retv;
		}

		private function Access($from)
		{
			$da = $this->db->prepare('SELECT `to` FROM paths WHERE `from` = ?');
			$da->execute([$from]);
			$diraces = $da->fetchAll(\PDO::FETCH_COLUMN);
			return $diraces;
		}

		public function FromDestinations()
		{
			$q = $this->db->prepare("SELECT DISTINCT `from` FROM paths;");
			$q->execute();

			$retv = [];
			foreach($q->fetchAll(\PDO::FETCH_OBJ) as $d)
			{
				if(!is_object($d)) continue;
				
				$str = '"'.str_replace("'", "''", $d->from).'"';
				
				if(!in_array($str, $retv))
					$retv[] = $str;
			}
			return implode(", ", $retv);
		}
		public function ToDestinations()
		{
			$q = $this->db->prepare("SELECT DISTINCT `to` FROM paths;");
			$q->execute();

			$retv = [];
			foreach($q->fetchAll(\PDO::FETCH_OBJ) as $d)
			{
				if(!is_object($d)) continue;
				
				$str = '"'.htmlentities($d->to).'"';
				
				if(!in_array($str, $retv))
					$retv[] = $str;
			}
			return implode(", ", $retv);
		}
	}