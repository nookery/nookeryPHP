## 使用说明和开发规范

### 一、开发规范
整理自[PSR-*](http://www.kancloud.cn/thinkphp/php-fig-psr/3140)，
不考虑PHP5.3（含）以前的版本
#### 1. 概览<hr>
- PHP代码文件必须以<code><?php</code>或<code><?=</code>标签开始；
- PHP代码文件必须以不带BOM的UTF-8编码；
- PHP代码中应该只定义类、函数、常量等声明，或其他会产生 从属效应 的操作（如：生成文件输出以及修改.ini配置文件等），二者只能选其一；
- 命名空间以及类必须符合 PSR 的自动加载规范：PSR-0 或 PSR-4 中的一个；
- 类的命名必须遵循<code>StudlyCaps</code>大写开头的驼峰命名规范；
- 类中的常量所有字母都必须大写，单词间用下划线分隔；
- 方法名称必须符合<code>camelCase</code>式的小写开头驼峰命名规范。

#### 2. 文件<hr>
- ##### 2.1 PHP标签
    - PHP代码必须使用<code><?php ?></code>长标签或<code><?= ?></code> 短输出标签； 一定不可使用其它自定义标签。

    
- ##### 2.2 字符编码
    - PHP代码必须且只可使用不带BOM的UTF-8编码。

    
- ##### 2.3 从属效应（副作用）
一份PHP文件中应该要不就只定义新的声明，如类、函数或常量等不产生从属效应的操作，要不就只有会产生从属效应的逻辑操作，但不该同时具有两者。<br>  
“从属效应”(side effects)一词的意思是，仅仅通过包含文件，不直接声明类、 函数和常量等，而执行的逻辑操作。<br>  
“从属效应”包含却不仅限于：生成输出、直接的 require 或 include、连接外部服务、修改 ini 配置、抛出错误或异常、修改全局或静态变量、读或写文件等。<br>  
以下是一个范例，一份包含声明以及产生从属效应的代码：

    ````
    <?php
    // 从属效应：修改 ini 配置
    ini_set('error_reporting', E_ALL);
    
    // 从属效应：引入文件
    include "file.php";
    
    // 从属效应：生成输出
    echo "<html>\n";
    
    // 声明函数
    functionfoo(){
        // 函数主体部分
    }
    ````
    
	下面是一个范例，一份只包含声明不产生从属效应的代码：

    ````
    <?php
    // 声明函数
    functionfoo(){
        // 函数主体部分
    }
    
    // 条件声明**不**属于从属效应
    if (! function_exists('bar')) {
        functionbar(){
            // 函数主体部分
        }
    }
    ````
    
#### 3. 命名空间和类<hr>
命名空间以及类的命名必须遵循 PSR-0.

根据规范，每个类都独立为一个文件，且命名空间至少有一个层次：顶级的组织名称（vendor name）。

类的命名必须遵循<code>StudlyCaps</code>大写开头的驼峰命名规范。

例如：

	<?php
	namespace Vendor\Model;
	
	class Foo{
	}
	
#### 4. 类的常量、属性和方法<hr>
此处的“类”指代所有的类、接口以及可复用代码块（traits）

- ##### 4.1. 常量
类的常量中所有字母都必须大写，词间以下划线分隔。 参照以下代码：

    ````
    <?php
    namespace Vendor\Model;
    
    class Foo{
        const VERSION = '1.0';
        const DATE_APPROVED = '2012-06-01';
    }
    ````
    
- ##### 4.2. 属性
类的属性命名可以遵循大写开头的驼峰式 ($StudlyCaps)、小写开头的驼峰式 ($camelCase) 又或者是 下划线分隔式 ($under_score)，本规范不做强制要求，但无论遵循哪种命名方式，都应该在一定的范围内保持一致。这个范围可以是整个团队、整个包、整个类或整个方法。


- ##### 4.3. 方法
方法名称必须符合 camelCase() 式的小写开头驼峰命名规范。

#### 5. 代码风格<hr>
- 使用2或4个空格符或者tab键锁进，不可以空格和tab混用。
- 每行的字符数应该软性保持在80个之内， 理论上一定不可多于120个， 但一定不能有硬性限制。
- 每个 namespace 命名空间声明语句和 use 声明语句块后面，必须插入一个空白行。
- 类的属性和方法必须添加访问修饰符（private、protected 以及 public）， abstract 以及 final必须声明在访问修饰符之前，而 static必须声明在访问修饰符之后。
- 控制结构的关键字后必须要有一个空格符，而调用方法或函数时则一定不能有。
- 例子：


    ````
    <?php
    namespace Vendor\Package;
    
    use FooInterface;
    use BarClass as Bar;
    use OtherVendor\OtherPackage\BazClass;
    
    classFooextendsBarimplementsFooInterface
    {
        public functionsampleFunction($a, $b = null)
        {
            if ($a === $b) {
                bar();
            } elseif ($a > $b) {
                $foo->bar($arg1);
            } else {
                BazClass::bar($arg2, $arg3);
            }
        }
    
        final public static functionbar()
        {
            // method body
        }
    }
    ````

- 纯PHP代码文件必须省略最后的 ?> 结束标签。
- 所有关键字必须全部小写，常量 true 、false 和 null 也必须全部小写。
- namespace 声明后 必须 插入一个空白行。
- 所有 use 必须 在 namespace 后声明。
- 每条 use 声明语句 必须 只有一个 use 关键词。
- use 声明语句块后 必须 要有一个空白行。
- 例如：


    ````
    <?php
    namespace Vendor\Package;
    
    use FooClass;
    use BarClass as Bar;
    use OtherVendor\OtherPackage\BazClass;
    
    // ... additional PHP code ...
    ````
### 二、框架的使用说明

