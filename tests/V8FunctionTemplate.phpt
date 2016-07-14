--TEST--
v8\FunctionTemplate
--SKIPIF--
<?php if (!extension_loaded("v8")) {
    print "skip";
} ?>
--FILE--
<?php
/** @var \Phpv8Testsuite $helper */
$helper = require '.testsuite.php';

require '.v8-helpers.php';
$v8_helper = new PhpV8Helpers($helper);

require '.tracking_dtors.php';

// Tests:

$isolate = new \v8\Isolate();

$callback = function () {
    print("hello word to js from PHP\n");
};

$function_template = new \v8\FunctionTemplate($isolate);

$helper->header('Object representation');
debug_zval_dump($function_template);
$helper->space();

$helper->assert('FunctionTemplate extends Template', $function_template instanceof \v8\Template);
$helper->line();

$print_func_tpl = new \v8\FunctionTemplate($isolate, function (\v8\FunctionCallbackInfo $info) {
    $context = $info->GetContext();

    $out = [];

    foreach ($info->Arguments() as $arg) {
        if ($arg->IsUndefined()) {
            $out[] = '<undefined>';
        } elseif ($arg->IsNull()) {
            $out[] = var_export(null, true);
        } elseif ($arg->IsTrue() || $arg->IsFalse()) {
            $out[] = var_export($arg->BooleanValue($context), true);
        } else {
            $out[] = $arg->ToString($context)->Value();
        }
    }

    echo implode('', $out);
});



$function_template->SetClassName(new \v8\StringValue($isolate, 'TestFunction'));


$helper->header('Object representation');
debug_zval_dump($function_template);
$helper->space();

$helper->header('Accessors');
$helper->method_matches($function_template, 'GetIsolate', $isolate);
$helper->space();


$helper->header('Instance template');
$instance_template = $function_template->InstanceTemplate();
debug_zval_dump($instance_template);
$helper->method_matches($function_template, 'InstanceTemplate', $instance_template);
$helper->space();

$instance_template_1 = $function_template->InstanceTemplate();

$instance_template_2 = $function_template->InstanceTemplate();

$extensions = [];
$global_template = new v8\ObjectTemplate($isolate);

$value = new v8\StringValue($isolate, 'TEST VALUE 111');

$global_template->Set(new \v8\StringValue($isolate, 'test'), $value);
$global_template->Set(new \v8\StringValue($isolate, 'func'), $function_template);
$global_template->Set(new \v8\StringValue($isolate, 'print'), $print_func_tpl, \v8\PropertyAttribute::DontDelete);


$context = new v8\Context($isolate, $extensions, $global_template);


$source    = '
print("Hello, world!\n");
print(s, " ", o,"\n");
typeof func()
';
//$source    = 'func(); func(); func(); func()';
$file_name = 'test.js';

$isolate2 = new \v8\Isolate();
$context2 = new v8\Context($isolate2);

$global = $context->GlobalObject();

$s = new \v8\StringValue($isolate, 'test');
$s2 = new \v8\StringValue($isolate2, 'test 2');

$o = new \v8\ObjectValue($context);
$o2 = new \v8\ObjectValue($context2);

$global->Set($context, new \v8\StringValue($isolate, 's'), $s);
try {
  $global->Set($context, new \v8\StringValue($isolate, 's2'), $s2);
} catch (Exception $e) {
  $helper->exception_export($e);
}

$global->Set($context, new \v8\StringValue($isolate, 'o'), $o);

try {
  $global->Set($context, new \v8\StringValue($isolate, 'o2'), $o2);
} catch (Exception $e) {
  $helper->exception_export($e);
}

$helper->value_matches_with_no_output($isolate, $isolate2, false);
$helper->value_matches_with_no_output($isolate, $isolate2, true);

$res = $v8_helper->CompileRun($context, $source);

debug_zval_dump($res->IsFunction());

if ($res->IsFunction()) {
    $func = $res->ToObject($context)->GetConstructorName();
    debug_zval_dump($func);
}

debug_zval_dump($res->ToString($context)->Value());





?>
--EXPECT--
Object representation:
----------------------
object(v8\FunctionTemplate)#5 (1) refcount(2){
  ["isolate":"v8\Template":private]=>
  object(v8\Isolate)#3 (1) refcount(2){
    ["snapshot":"v8\Isolate":private]=>
    NULL
  }
}


FunctionTemplate extends Template: ok

Object representation:
----------------------
object(v8\FunctionTemplate)#5 (1) refcount(2){
  ["isolate":"v8\Template":private]=>
  object(v8\Isolate)#3 (1) refcount(3){
    ["snapshot":"v8\Isolate":private]=>
    NULL
  }
}


Accessors:
----------
v8\FunctionTemplate::GetIsolate() matches expected value


Instance template:
------------------
object(v8\ObjectTemplate)#8 (1) refcount(2){
  ["isolate":"v8\Template":private]=>
  object(v8\Isolate)#3 (1) refcount(4){
    ["snapshot":"v8\Isolate":private]=>
    NULL
  }
}
v8\FunctionTemplate::InstanceTemplate() doesn't match expected value


v8\Exceptions\GenericException: Isolates mismatch
v8\Exceptions\GenericException: Isolates mismatch
Expected value matches actual value
Expected value is not identical to actual value
Hello, world!
test [object Object]
bool(false)
string(6) "object" refcount(1)