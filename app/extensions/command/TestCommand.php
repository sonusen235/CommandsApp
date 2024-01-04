<?php

// app/commands/SampleCommand.php

namespace app\extensions\command;
use lithium\console\Command;

class TestCommand extends Command {

    public function run() {
    	// echo "I am here";exit;
        print_r($this->request->params);exit;
        // Access command line parameters using $argv
        $argument1 = $this->request->argv[1] ?? null;
        $argument2 = $this->request->argv[2] ?? null;

        $this->out("Argument 1: $argument1");
        $this->out("Argument 2: $argument2");
    }
}

?>