<?php
namespace BITStudio\Lazy;
use Illuminate\Support\Facades\DB;
use Illuminate\Filesystem\Filesystem;

class Tester {
	protected $name;
	protected $stub;
	protected $content;
	protected $fileSystem;

	public function run($name, $table)
	{
		$this->name = $name;
		$this->fileSystem = new Filesystem();
		$classNameSpace = "App\\Http\\Controller";

		$this->stub = file_get_contents(__DIR__."/stubs/controller.stub");
		$this->content = str_replace('$CLASS_NAMESPACE$', $classNameSpace, $this->stub);

		$this->content = str_replace('$CLASS$', $name, $this->content);

		$this->content = str_replace('$TABLE_NAME$', $table, $this->content);

		$column =  DB::select("describe $table");

		$buildVarStore = "\$data = [".PHP_EOL;

		foreach ($column as $key => $value) {
			$convert = (array) $value;
			$buildVarStore .= "\t\t\t'{$convert['Field']}' => @\$request->{$convert['Field']},".PHP_EOL;
		}

		$buildVarStore .= "\t\t];".PHP_EOL;

		$this->content = str_replace('$VAR_STORE$', $buildVarStore, $this->content);
		// dd();
		$path = app_path("Http/Controllers/Test001Controller.php"); 
		$this->fileSystem->put($path, $this->content);
		return $this;
	}
}