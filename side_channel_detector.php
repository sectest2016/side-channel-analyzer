<?php

include_once(dirname(__FILE__) . '/dataflow_side_channel_analysis.php');
include_once(dirname(__FILE__) . '/TaintPHP/PHP-Parser-master/lib/bootstrap.php');
include_once(dirname(__FILE__) . '/TaintPHP/TaintAnalysis/taint_analysis.php');
include_once(dirname(__FILE__) . '/TaintPHP/CallGraph/CallGraph.php');
include_once(dirname(__FILE__) . '/TaintPHP/CFG/CFG.php');

$projectPath = $argv[1];

// Iterating over all php files in a project path.
$Directory = new RecursiveDirectoryIterator($projectPath);
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
$Regex->rewind();

// Map from filenames to CFG information.
$cfgInfoMap = array();

// Construct CFG map.
while($Regex->valid()) {
        // Regex iterator contains an array of a single element for each file.
        $fileName = $Regex->current()[0];
	
	// Obtain the CFGs of the main function, auxiliary functions and function signatures.
	$fileCFGInfo = CFG::construct_file_cfgs($fileName);
	$cfgInfoMap[$fileName] = $fileCFGInfo;
	$Regex->next();
}

// Construct call graphs, perform taint analysis and side channel detection.
$Regex->rewind();

$callGraph = new CallGraph();

while($Regex->valid()) {

        $fileName = $Regex->current()[0];
        print "==== STARTING CALL GRAPH CONSTRUCTION: " . $fileName . " ====\n";
	$callGraph->addFileCallGraphInfo($cfgInfoMap[$fileName]);

	//print "==== STARTING TAINT ANALYSIS ====\n";
	//$file_tainted_maps = taint_analysis($fileCFGInfo);

	//print "==== STARTING SIDE-CHANNEL DETECTION ====\n";

	//dataflow_side_channel_detection($fileCFGInfo, $file_tainted_maps);
	$Regex->next();
}

?>