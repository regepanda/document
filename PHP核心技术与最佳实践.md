## <b>PHP内核中的变量</b>
#### <font color='red'>PHP变量在内核中的存储方式</font>
```
(1).php是怎样实现一个变量可以保存任何的数据类型。在C里面有一个PHP变量的结构体，里面有个保存变量的
成员变量，为一个联合体，此联合体的成员变量可以存储任何数据类型
(2).Zend引擎是怎么知道这个变量保存的什么类型呢。在c里面定义了一系列宏，每个宏定义对应php变量的类型
typedef union _zvalue_value {
	long lval;					整形变量
	double dval;				小数
	struct {
		char *val;
		int len;
	} str;                      字符串
	HashTable *ht;				/* hash table value */
	zend_object_value obj;      对象
	zend_ast *ast;
} zvalue_value;

struct _zval_struct {
	/* Variable information */
	zvalue_value value;		   存储php变量的值，为一个联合体
	zend_uint refcount__gc;    计数器，计数有多少个变量引用当前变量
    zend_uchar type;	       变量类型 
	zend_uchar is_ref__gc;     是否是引用集合
};
```
#### <font color='red'>引用计数器与写时复制</font>
```
(1).php不支持指针，我们希望两个变量同时指向同一快内存。zval结构体有两个成员变量用于引用计数器
，is_ref表示变量是否是引用集合，refcount计算指向该引用集合的变量个数，也就是有多少个变量共用当
前变量的内存。
(2).因为在php中简单的复制是非常消耗内存的，所以引入写时复制就是为了解决这一问题。原理就是当变量的值
改变时才进行内存的复制
```
## <b>PHP内核中的HashTable</b>
#### <font color='red'>PHP内核HashTable的数据结构</font>
```
(1).当索引是整数的时候，php把索引保存到bucket的结构体的h成员变量中，然后nKeyLength=0，表示这个
索引是一个整数而不是字符串。$a=[1,2,3,4]
(2).当nKeyLength>0时，表示索引是字符串，$a=['a'=>1, 'b'=>2, 'c'=>3]。但是h还是有值保存的是索引
hash过后的值，即关联数组的下标。
(3).通过HashTable来管理bucket，其实就是通过hash函数把索引处理成一个int数，通过这个int下标定位数
组的某一个元素。其实hash算法还是取于
代码：h = hash(key);
      pos = h % nTableSize;
      bucket = arBuckets[pos];
      
typedef struct bucket {
	ulong h;						保存经过hash函数处理之后的hash值
	uint nKeyLength;                保存索引key的长度
	void *pData;                    指向要保存的内存快地址(通过malloc分配内存)
	void *pDataPtr;                 保存指针数据（如果变量是指针类型，直接保存到该成员变量中，而不用调用malloc分配内存，然后pdata指向pdataptr）
	struct bucket *pListNext;       指向双向链表的下一个元素
	struct bucket *pListLast;       指向双向链表的上一个元素
	struct bucket *pNext;           指向具有同一个hash值的下一个元素
	struct bucket *pLast;           指向具有同一个hash值的上一个元素
	const char *arKey[1];           保存索引(key)，而且必须为最后一个成员
} Bucket;

typedef struct _hashtable {
	uint nTableSize;                记录bucket数组的大小
	uint nTableMask;                
	uint nNumOfElements;            记录HashTable中元素个素
	ulong nNextFreeElement;         下一个可用bucket位置
	Bucket *pInternalPointer;	    遍历HashTable元素
	Bucket *pListHead;              双链表表头
	Bucket *pListTail;              双链表表尾
	Bucket **arBuckets;             bucket数组
} HashTable;
(4).综上，一个hashtable包含一个bucket数组，当一个字符串key经过hash函数处理返回一个int整数，通过int索引定位
到bucket数组中取出对应元素。如果有冲突，用双向链表解决。
```

## <b>缓存详解</b>
#### <font color='red'>认识缓存</font>
```
(1).位于速度相差较大的两种介质之间。比如CPU速度是内存的10倍，内存又是硬盘的10倍，所以可以在CPU与内存或者
内存与硬盘之间加入缓存。
(2).在有些缓存应用中，如果数据的频繁更新会导致缓存的命中率大大降低。这时应该考虑缓存的合理性
(3).缓存更新策略。
    a.FIFO,最先进入缓存的数据在缓存不够的时候会首先被清理出去
    b.LFU,最少使用的会被清理
    c.LRU,最近最少使用的会被清理，主要是比较每个缓存元素的时间戳与当前时间戳，找出差值最大的那个
```
#### <font color='red'>Opcode</font>
```
(1)、php在程序运行完成后，内存会马上释放，基本上所有数据在此时销毁。这样便无法复用数据，每次php请求都会重复【请求-翻译-执行的这】
一过程，Opcode把php代码编译成一种中间语言缓存起来。就不用重复执行以下前三步。
	a、Scanning(Lexing) ,将PHP代码转换为语言片段(Tokens)、
	b、Parsing, 将Tokens转换成简单而有意义的表达式
	c、Compilation, 将表达式编译成Opcodes
	d、Execution, 顺次执行Opcodes，每次一条，从而实现PHP脚本的功能。
(2)、eAccelerator常驻内存。对应php版本的linux源码下载：http://eaccelerator.net/。服务器第一次请求php问件时，会对php文件的Opcode
进行缓存，由zend虚拟机直接执行，节省语法解析的消耗。
```
## <b>Memcached使用与实践</b>
特点：1、协议简单。2、基于libevent的事件处理。2、内置内存存储方式。4、采用互不通信的分布式。
```
```

## <b>高性能网站架构方案</b>
#### <font color='red'>MySQL主从复制</font>
```
(1)、主从复制的优点
	a、增加健壮性。主服务器出现问题时，可以切换到从服务器作为备份
	b、优化响应时间，主写从读。不要同时在主从服务器上进行更新操作，这样可能会起冲突
	c、在从服务器备份过程中，主服务器继续处理更新
(2)、主从复制工作原理
	a、主服务器将用户对数据库更新的操作以二进制的形式保存到日志文件中，然后由binlog dump线程将日志文件传输给从服务器
	b、从服务器通过一个I/O线程将主服务器二进制日志文件中的更新操作复制到一relay log中继日志文件中
	c、从服务器通过另一个SQL线程将relay log中继日志文件中的操作依次在本地执行，从未实现主从同步
(3)、主从复制配置
	a、主服务器版本不能高于从服务器版本
	b、在主服务器上为从服务器设置一个连接账户，假设域为mydomain.com,用户名：repl，密码：pass4slave
	   语句：grant replication slave on *.* to 'repl'@'%.mydomain.com'
	        identified by 'pass4slave';
    c、配置主服务器。修改my.conf配置文件
       [mysqld]
       log-bin = mysqld-bin
       server-id = 1
    d、重启主服务器，运行show master status查看信息
    e、配置从服务器，与主服务器类似，提供一个唯一的serverid，不能跟主服务器一样
       [mysqld]
       log-bin = mysqld-bin
       server-id = 2
    f、重启从服务器。让从服务器连接主服务器，并开始重做主服务器binlog文件中的事件
    g、指定主服务器信息。使用change master to语句指定主服务器信息，不介意在my.conf中修改
       change master to master_host = '192.168.1.100',
       master_ user = 'repl',
       master_ password = 'pass4slave',
       master_ log_ file = 'mysql -bin. 000001',
       master_ log_ pos = 0;
    h、查看从服务器的设置是否正确。show slave status\G
```
