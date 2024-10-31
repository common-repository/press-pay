<?php
// Don't redeclare the Class if it already exists.
if (!class_exists( 'Mixer' ))
{
abstract class Mixer {

  protected $methods = array();
  protected $mixins = array();
  protected $priorities = array();

  /**
   ** @description By adding a mixin, the extending class will automatically adopt all of a mixin's methods.
   ** @param object $mixin - The instantiated object that's methods should be adopted by the extending class.
   ** @security You are expected to know if there will be a conflict in which two mixins share the same method name. If 
   **      this conflict occurs, use the <a href="#setPriorities">setPriorities()</a> method to specify which method 
   **      should take precedence. Mixer will not handle the conflict automatically on its own. 
   **/
  public function addMixin($mixin){
    if (!is_object($mixin)){
      throw new Exception("The mixin is not valid because it is not an object.");
    }
    $name = get_class($mixin);
    $this->mixins[$name] = $mixin;
    $methods = get_class_methods($name);
    $this->methods[$name] = $methods;
  }

  /**
   ** @description Allows multiple mixins to be added at once. By adding a mixin, the extending class will 
   **      automatically adopt all of a mixin's methods.
   ** @param array $mixin - An array of instantiated objects whose methods should be adopted by the extending class.
   ** @security You are expected to know if there will be a conflict in which two mixins share the same method name. If 
   **      this conflict occurs, use the <a href="#setPriorities">setPriorities()</a> method to specify which method 
   **      should take precedence. Mixer will not handle the conflict automatically on its own. 
   **/
  public function addMixins(array $mixins){
    foreach ($mixins as $mixin){
      $this->addMixin($mixin);
    }
  }   

  /**
   ** @description Gets the class's current mixins by name
   ** @return An array of mixin class names.
   **/
  public function getMixins(){
    return array_keys($this->methods);
  }

  /**
   ** @description Manages conflicts for the mixins.
   ** @param array $priorities - The method name as the key, the class name that has priority in a conflict as 
   **      the value.
   ** @note Once a method has been assigned to a class, it cannot be reassigned to a different class at a later point. 
   **      This is done to minimize potential bugs due to dynamic prioritization.
   **/
  public function setPriorities(array $priorities){
    $classNames = array_keys($this->methods);
    $setPriorities = array_keys($this->priorities);
    foreach ($priorities as $method=>$class){
      if (!in_array($class, $classNames)){
        throw new Exception("$class is not a valid mixin. To make $class a mixin, use the addMixin method.");
      }
      if (!in_array($method, $this->methods[$class])){
        throw new Exception("$class does not have a method named '$method'.");
      }
      if (in_array($method, $setPriorities)){
        $assigned = $this->priorities[$method];
        throw new Exception("$method has already been assigned to $assigned and cannot be reassigned.");
      }
    }
  $this->priorities = $priorities;
  }

  /**
   ** @description A magic method that calls the mixin methods automatically. This method should not be 
   **      called directly.
   ** @param string $methodName - The name of the mixin method
   ** @param array $arguments - The arguments for the method
   ** @return The return value will vary depending on the function called.
   **/
  public function __call($methodName, array $arguments){
    foreach ($this->methods as $className=>$methods){
      if (in_array($methodName, $methods)){
        if (
           (in_array($methodName, array_keys($this->priorities))) &&
           ($className == $this->priorities[$methodName])
        ){
          return call_user_func_array(array($className, $methodName), $arguments);
        } else if (!in_array($methodName, array_keys($this->priorities))){
          return call_user_func_array(array($className, $methodName), $arguments);
        }
      } 
    }
    $mixins = (sizeof($this->methods) > 0) ? implode(', ', array_keys($this->methods)) : 'No mixins are listed.';
    throw new Exception("$methodName is not a method. Your current mixins are: $mixins");
  }
}
}
?>
