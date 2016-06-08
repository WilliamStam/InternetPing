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
		
		
		$data = preg_split("/\\r\\n|\\r|\\n/", $data);
		
		$t = array();
		$max = 0;
		
		$hours = array();
		
		foreach ($data as $item){
			$item = preg_split("/\\t/",$item);
			$date = $item[0];
			$ping = $item[1];
			
			$max = $max<$ping?$ping:$max;
		
			//test_array(date("H",strtotime($date))); 
			$hourStart = "";
			if (!in_array(date("H",strtotime($date)),$hours)){
				$hourStart = date("H",strtotime($date));
			}
			$hours[] = date("H",strtotime($date));
			if ($date) {
				$t[] = array($date,$ping,$hourStart);
			}
		}
		
		
		$tmpl = new \template("template.twig");
		$tmpl->page = array(
			"section"    => "home",
			"sub_section"=> "home",
			"template"   => "home",
			"meta"       => array(
				"title"=> "Internet | ".date("D, d M Y H:i:s"),
			),
			
		);
		$tmpl->file=$file;
		$tmpl->logs=$logs;
		$tmpl->data=$t;
		$tmpl->max=$max;
		$tmpl->output();
	}
	
	
	
}
