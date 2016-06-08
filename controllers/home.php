<?php
namespace controllers;
use \timer as timer;
class home extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		$file = isset($_GET['log'])&&$_GET['log']?$_GET['log']:"log-".date("Y-m-d").".log";
		$folder = $this->cfg['folder'];
		$data = @file_get_contents($folder.$file);
		
		$logs = array();
		foreach (glob($folder . "log-*.log") as $log) {
			
			$logs[] = str_replace($folder,"",$log);
			
		}
		
		$stages = array(
			"0-20",
			"20-50",
			"50-100",
			"100-150",
			"150-200",
			"200+",
			"failed"	
		);
		$statsStages = array();
		$stagesExploded = array();
		foreach ($stages as $key=>$item){
			$stagesExploded[$key] = explode("-",$item);
			$statsStages[$key] = 0;
		}
		
		//test_array($statsStages); 
		
		
		$data = preg_split("/\\r\\n|\\r|\\n/", $data);
		
		$t = array();
		$max = 0;
		$totalDown = 0;
		$hours = array();
		
		
		
		//test_array($stagesExploded); 
		
		foreach ($data as $item){
			$item = preg_split("/\\t/",$item);
			$date = $item[0];
			$ping = $item[1];
			
			$max = $max<$ping?$ping:$max;
			
			
			
			if ($ping=="-")$totalDown = $totalDown + 1;
			$status = "";
			
			foreach ($stagesExploded as $key => $stage_data){
				if ($ping >= $stage_data[0] && $ping < $stage_data[1]){
					$status = $key;
					break;
				};
				 
				
				
				if (!isset($stage_data[1]) && strpos($stage_data[0],"+")){
					if ($ping > str_replace("+","",$stage_data[0]) ){
						$status = $key;
						break;
					}
				}
			}
			if ($ping=="-"){
				$status = count($stages)-1;
			}
			
			
			//test_array($status); 
			
			//test_array(date("H",strtotime($date))); 
			$hourStart = "";
			if (!in_array(date("H",strtotime($date)),$hours)){
				$hourStart = date("H",strtotime($date));
			}
			$hours[] = date("H",strtotime($date));
			
			
			
			if ($date) {
				$statsStages[$status] = $statsStages[$status] +1;
				$t[] = array($date,$ping,$status,$hourStart);
			}
		}
		
		//test_array($t); 
		
		$total = count($t);
		$stats = array(
				"total"=>$total,
				"failed"=>$totalDown,
				"percent"=>($totalDown && $total)?($totalDown/$total)*100:0
				
		);
		$stats["stages"] =$statsStages;
		
		
		//test_array($stats);
		
		
		
		//test_array($t); 
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "home",
			"sub_section"=> "home",
			"template"   => "home",
			"meta"       => array(
				"title"=> "Internet | ".date("D, d M Y H:i:s"),
			),
			
		);
		
		
		
		$tmpl->stages=$stages;
		$tmpl->stats=$stats;
		$tmpl->file=$file;
		$tmpl->logs=$logs;
		$tmpl->data=$t;
		$tmpl->max=$max;
		$tmpl->output();
	}
	
	
	
}
