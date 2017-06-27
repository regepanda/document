## 代码包
package 是代码包的声明，go 规定其为路径最后一个元素
```
package someName
import basic/set
import (la cnet/ctcp)//起一个别名
```
注意无论命宁源码文件存在哪个包中，都必须申明为main,这里简单说明下命宁源码文件和库源码文件的区别  
1.命令源码文件  
都被申明为main包，可以通过go run直接运行，通常命宁源码会单独放在一个代码包中，但是注意同一个包中所有的代码文件的包必须一致，否则无法build和install，这就导致了库源码文件不能和命宁放在一起  
当有且仅有一个命令源码时，可移植性instakk 可以在当前工作区域生成一个可执行文件/bin
逐一要配置go工作去变量，否则会install失败
2.库源码文件  
在库源码文件编译后可以声称一个库文件，存放到pkg中
## 编程基础
常用的，你所想到的类型都有。
潜在类型，每一个类型都有一个潜在类型，如果是一个预定义类型，那么就是他本身，就像隐式类型转换  
## 操作符
就那些，有些需要被注意
1.使用一个变量前（比如=）必须先声明类型，也可以用:=
array1 := [2]string{"12","34"}

## 数组
声明一个长度为n 类型为t  [n]t  t可以是一个复杂的类型
两个数组长度不同，也是不同类型  
值表示法 [2]str{"xx", "yy"}  
在go中一个数组的变量名并不像c语言那样代表的是数组第一个元素，而是代表整个数组，当把一个数组赋值给另外一个时，是隐含拷贝一个备份的，如果要引用，则使用&  
因为长度属于类型，所以可以使用len()获取长度  
### 切片
对于一个切片类型，应该这样表示
[]SomeType
可以见到长度并不是切片类型的一部分，长度式可变的，相同类型的切片值，可能长度不一样，相当于没有长度的数组  
一个切片可以用len来获得长度   
一个切片一旦被初始化，就会保留一个对某个数组的引用，多个切片值可以公用一个底层的数组，也就是说，和python不同，切片是不会拷贝的  
一个切片的容量就是它的底层数组可以使用cap()内置函数来得到
a := [...]string{"c","b","x"}
slice1 := a[:2]  

slcie3 :=append(slice1,"Ruby","Rail")//会在原有窗口基础上加两个，形成一个新的窗口，但是并不会改变slice1  
copy作用是把原切片复制到目的切片，并返回被复制的元素的数量

数组切片加了一系列管理功能再数组上
如果空间不够，那么使用append会自动分配新的空间在移动过去
直接创建切片
mySlice := [] int(1,2,3)
mySlice := make([] int(1,2,3))




## 字典
键类型k值T  
map[k]T   
map[int]string  
map[string]string{"vim":true,"xx":true}

myMap := make(map[string] int,100)
	myMap["x"] = 1
	value , ok := myMap["1234"]
	if !ok{
		fmt.Println("error!")
	}
	fmt.Println(value)
使用一个map前，必须要对其进行空间分配

## 循环语句
go只支持for
普通场景
sum := 0
for i:=0; i<10 ;i++{
  sum+=i
}

无限循环的场景
sum := 0
for{
  sum++

}

多赋值语句
for i,j := 0,5; i<5; i,j = 5,0
需要对应赋值





## 函数
函数可以多个返回
func Add(name string)(status int, message string){

}
    函数名    参数表            返回值表
首字母大写public 小写private
当返回多个值时
可以省略前面的参数类型声明，默认使用最近最后一个
如果我在一个函数外不太关心某个返回值，直接用_就行了


使用和引入
import "mypath"
c := mymath.Add()
这样可以调用一个包中的函数

不定参数
本质上就是一个切片
```
func myfunc(args ...int){
  for _,arg := range args{

  }
}
```
 如果要传匿名，只要不写函数名就行了，匿名函数具有函数级别作用域，即是闭包

## 错误处理
要实现自定义的错误，只需要继承error接口即可
例子
defer file.Close()
defer后的语句将在函数结束后被执行，这个语句可以是一匿名函数，最后一个defer将会最先执行，先进后出

panic(interface{})
  当在一个函数执行过程中调用这个函数是，正常流程将会终止(defer还是会执行)，逐级向上调用panic流程，直到recover，很像throw
recover(interface{})
  recover可以用来终结这个流程,一般应该出现再一个defer中，如果一个程序再panic后没有进行recover，那么就会终止
defer func(){
  if r := recover();r!=nil{
    //这个recover类型是当时传入的类型
  }
}

## 面向对象
go语言的面向对象语法其实就是面向对象在c/c++中的实现，其实就是给一个struct添加方法
```
type someName struct
func (this struct) SomeFunc(b int) bool{
  return true
}
```
这里声明了一个类，并为它添加了一个SomeFunc的方法
有时候也必须要求对象被引用/指针传递
```
func (this *struct) SomeFunc(b int) bool{

}
```

### 初始化
react1 := new(React)  
react2 := &Rect{}  
react3 := &Rect{width:100,heitht:100}  
Go中没有构造函数的概念，创建通常由一个全局创建函数来完成，返回一个新的对象  
### 匿名组合
```
type Foo struct{
  Base
}
```
这种方法就直接Base中的成员全部写了过来，使用Base的成员，就像使用自己的一样
如果使用*Base，任然可以实现派生，但是创建的时候需要额外提供一个Base的指针
就是可以再运行时直接组合一个对象进来

```
type Job struct{
  Command
  *log.Logger  //所有成员方法都可以调用Logger里面的方法，比如Logger中有个add()
}
job.add("xx")
```

### 接口
一个类实现了一个接口的所有方法，就可以认为这个类实现了该接口，而不需要显式的继承  
然后就可以以一个接口的身份去实例化对象并生成对象成员函数  

将某个对象赋值给接口  
var b SomeType = &a  
这样的话对于a中的成员函数传对象为值的，会自动变成引用传递  
var b SomeType = a  
这样还是会按照原来的样子  

接口赋值给接口  
如果接口a是接口b的子集，那么b可以赋值a，但是反过来是不行的

查询接口
```
if someVar,ok = file1.(someInterface);ok{

}
```
判断someVar实现了接口吗
```
if somaVar ,ok = file.(*someInterface )；ok{

}
```
判断somaVar是不是某个类型

v1.(type)直接获得这个变量的类型

接口也可以进行匿名组合，像类一样


## 并发
go SomeFunc(x,y int)  
使用协程执行函数任务，程序退出时并不等待协程  

通讯方式  
var chanName chan int
ch :=make(chan int)

ch<-value  将一个数据写入channel，向其中写入数据会导致程序阻塞，直到这个通道被读取
value := <- ch 读取也是阻塞的


select
var v int
ch:=make(chan int,1024) //第二个参数是缓冲区
select {
  case ch <- 0;
  case v=  <- ch;
  default:
  //默认处理流程
}

可以用这个select来处理超市问题
启动一个协程计算时间
select其中一个case来获取这个倒计时间
close(ch)

## 网络编程
