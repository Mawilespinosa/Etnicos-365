<?php
require 'vendor/autoload.php';
$class = 'Tests\Feature\PublicStoreTest';
echo "Class exists: " . (class_exists($class) ? 'yes' : 'no') . "\n";
if (class_exists($class)) {
    $r = new ReflectionClass($class);
    echo "Methods:\n";
    foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
        echo "  " . $m->name . "\n";
    }
}