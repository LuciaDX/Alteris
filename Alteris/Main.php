<?php

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use Symfony\Component\Filesystem\Filesystem;

require __DIR__ . "/vendor/autoload.php";

class ClassMethodOverride extends NodeVisitorAbstract {

    public function __construct(public string $file, public string $str, public string $method, public string $code){}

    public function enterNode(Node $node): void{
        if ($node instanceof Node\Stmt\ClassMethod) {
            if($node->name->name == $this->method){
                file_put_contents(
                    $this->file,
                    substr_replace($this->str,trim($this->code),$node->getStartFilePos(),$node->getEndFilePos()-$node->getStartFilePos()+1)
                );
            }
        }
    }
}

class ClassMethodAdd extends NodeVisitorAbstract {

    public function __construct(public string $file, public string $str, public string $method){}

    public function enterNode(Node $node): void{
        if ($node instanceof Node\Stmt\Class_) {
            file_put_contents(
                $this->file,
                substr_replace($this->str,rtrim($this->method),$node->getEndFilePos(),1)."}\n"
            );
        }
    }
}

class ClassPropertyOverride extends NodeVisitorAbstract {

    public function __construct(public string $file, public string $str, public string $property, public string $code){}

    public function enterNode(Node $node): void{
        if ($node instanceof Node\Stmt\Property) {
            if($node->props[0]->name->name == $this->property){
                file_put_contents(
                    $this->file,
                    substr_replace($this->str,trim($this->code),$node->getStartFilePos(),$node->getEndFilePos()-$node->getStartFilePos()+1)
                );
            }
        }
    }
}

class LastItemFind extends NodeVisitorAbstract {

    public array $data = [0,0];

    public function __construct(public string $file, public string $str, public string $type){}

    public function enterNode(Node $node): void{
        if ($node::class == $this->type) {
            $this->data = [$node->getStartFilePos(),$node->getEndFilePos()-$node->getStartFilePos()+1];
        }
    }
}

class ClassConstOverride extends NodeVisitorAbstract {

    public function __construct(public string $file, public string $str, public string $const, public string $code){}

    public function enterNode(Node $node): void{
        if ($node instanceof Node\Stmt\ClassConst) {
            if($node->consts[0]->name->name == $this->const){
                file_put_contents(
                    $this->file,
                    substr_replace($this->str,trim($this->code),$node->getStartFilePos(),$node->getEndFilePos()-$node->getStartFilePos()+1)
                );
            }
        }
    }
}

function ReplaceClassMethod(string $file, string $method, string $code): void{
    $str = file_get_contents(dirname(__FILE__) . "/Modified/".$file.".php");
    $file = dirname(__FILE__) . "/Modified/".$file.".php";
    $parser = (new ParserFactory())->createForNewestSupportedVersion();
    try {
        $ast = $parser->parse($str);
    } catch (Error $error) {
        echo "Parse error: {$error->getMessage()}\n";
        return;
    }
    $traverser = new NodeTraverser();
    $visitor = new ClassMethodOverride($file,$str,$method,$code);
    $traverser->addVisitor($visitor);
    $ast = $traverser->traverse($ast);
}

function AddClassMethod(string $file, string $method): void{
    $str = file_get_contents(dirname(__FILE__) . "/Modified/".$file.".php");
    $file = dirname(__FILE__) . "/Modified/".$file.".php";
    $parser = (new ParserFactory())->createForNewestSupportedVersion();
    try {
        $ast = $parser->parse($str);
    } catch (Error $error) {
        echo "Parse error: {$error->getMessage()}\n";
        return;
    }
    $traverser = new NodeTraverser();
    $visitor = new ClassMethodAdd($file,$str,$method);
    $traverser->addVisitor($visitor);
    $ast = $traverser->traverse($ast);
}

function ReplaceClassProperty(string $file, string $property, string $code): void{
    $str = file_get_contents(dirname(__FILE__) . "/Modified/".$file.".php");
    $file = dirname(__FILE__) . "/Modified/".$file.".php";
    $parser = (new ParserFactory())->createForNewestSupportedVersion();
    try {
        $ast = $parser->parse($str);
    } catch (Error $error) {
        echo "Parse error: {$error->getMessage()}\n";
        return;
    }
    $traverser = new NodeTraverser();
    $visitor = new ClassPropertyOverride($file,$str,$property,$code);
    $traverser->addVisitor($visitor);
    $ast = $traverser->traverse($ast);
}

function AddClassProperty(string $file, string $property): void{
    $str = file_get_contents(dirname(__FILE__) . "/Modified/".$file.".php");
    $file = dirname(__FILE__) . "/Modified/".$file.".php";
    $parser = (new ParserFactory())->createForNewestSupportedVersion();
    try {
        $ast = $parser->parse($str);
    } catch (Error $error) {
        echo "Parse error: {$error->getMessage()}\n";
        return;
    }
    $traverser = new NodeTraverser();
    $visitor = new LastItemFind($file,$str,Node\Stmt\Property::class);
    $traverser->addVisitor($visitor);
    $ast = $traverser->traverse($ast);
    $found = substr($str,$visitor->data[0],$visitor->data[1]);

    file_put_contents(
        $file,
        substr_replace($str,$found."\n\n".$property,$visitor->data[0],$visitor->data[1])
    );
}

function AddClassUse(string $file, string $use): void{
    $str = file_get_contents(dirname(__FILE__) . "/Modified/".$file.".php");
    $file = dirname(__FILE__) . "/Modified/".$file.".php";
    $parser = (new ParserFactory())->createForNewestSupportedVersion();
    try {
        $ast = $parser->parse($str);
    } catch (Error $error) {
        echo "Parse error: {$error->getMessage()}\n";
        return;
    }
    $traverser = new NodeTraverser();
    $visitor = new LastItemFind($file,$str,Node\Stmt\Use_::class);
    $traverser->addVisitor($visitor);
    $ast = $traverser->traverse($ast);
    $found = substr($str,$visitor->data[0],$visitor->data[1]);

    file_put_contents(
        $file,
        substr_replace($str,$found."\n\n".$use,$visitor->data[0],$visitor->data[1])
    );
}

function ReplaceClassConst(string $file, string $const, string $code): void{
    $str = file_get_contents(dirname(__FILE__) . "/Modified/".$file.".php");
    $file = dirname(__FILE__) . "/Modified/".$file.".php";
    $parser = (new ParserFactory())->createForNewestSupportedVersion();
    try {
        $ast = $parser->parse($str);
    } catch (Error $error) {
        echo "Parse error: {$error->getMessage()}\n";
        return;
    }
    $traverser = new NodeTraverser();
    $visitor = new ClassConstOverride($file,$str,$const,$code);
    $traverser->addVisitor($visitor);
    $ast = $traverser->traverse($ast);
}

function AddClassConst(string $file, string $const): void{
    $str = file_get_contents(dirname(__FILE__) . "/Modified/".$file.".php");
    $file = dirname(__FILE__) . "/Modified/".$file.".php";
    $parser = (new ParserFactory())->createForNewestSupportedVersion();
    try {
        $ast = $parser->parse($str);
    } catch (Error $error) {
        echo "Parse error: {$error->getMessage()}\n";
        return;
    }
    $traverser = new NodeTraverser();
    $visitor = new LastItemFind($file,$str,Node\Stmt\ClassConst::class);
    $traverser->addVisitor($visitor);
    $ast = $traverser->traverse($ast);
    $found = substr($str,$visitor->data[0],$visitor->data[1]);

    file_put_contents(
        $file,
        substr_replace($str,$found."\n\n".$const,$visitor->data[0],$visitor->data[1])
    );
}

$includes = [];

function main(): void{
    $includes = [];

    $filesystem = new Filesystem();
    $filesystem->remove(dirname(__FILE__) . "/Modified");
    $filesystem->mkdir(dirname(__FILE__) . "/Modified");
    $filesystem->mirror(dirname(__FILE__,2) . "/src",dirname(__FILE__) . "/Modified/src");
    $filesystem->mirror(dirname(__FILE__,2) . "/build",dirname(__FILE__) . "/Modified/build");
    $filesystem->mirror(dirname(__FILE__,2) . "/vendor",dirname(__FILE__) . "/Modified/vendor");
    $filesystem->mirror(dirname(__FILE__,2) . "/resources",dirname(__FILE__) . "/Modified/resources");

    foreach(glob(__DIR__ . "/Include/*") as $folder) {
        $filesystem->mirror($folder,dirname(__FILE__) . "/Modified/src/".str_replace(__DIR__ . "/Include/","",$folder));
        foreach(glob("$folder/*.php") as $file) {
            $name = str_replace(__DIR__ . "/Include/","",$file);
            $includes[] = "		require_once(__DIR__ . '/$name');";
        }
    }

    print("\n\x1b[38;5;226mAltering source files...\n\n");

    foreach(glob(__DIR__ . "/Overrides/*.php") as $filename) {
        include $filename;
        print("\x1b[38;5;200mLoading changes from \x1b[38;5;213m" . array_key_last(array_flip(explode("/", $filename))) . "\n");
    }

    file_put_contents(
        dirname(__FILE__) . "/Modified/src/PocketMine.php",
        str_replace(
            "require_once(\$bootstrap);",
            "require_once(\$bootstrap);\n".implode("\n",$includes)."\n",
            file_get_contents(dirname(__FILE__) . "/Modified/src/PocketMine.php")
        )
    );
}

main();

