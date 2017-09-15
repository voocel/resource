
## 名词解释
* Schema： 一种以文件形式存储的数据库模型骨架，不具备数据库的操作能力
* Model： 由Schema编译而成的假想（fancy）构造器，具有抽象属性和行为。Model的每一个实例（instance）就是一个document。document可以保存到数据库和从数据库返回。
* Instance： 由Model创建的实例。
## 概念解析
| SQL术语/概念   | MongoDB术语/概念  | 解释/说明  |
| ------------- |:-------------:| :-----|
| rdatabase     | database       |    - |
| table          | collection    |   数据库表/集合 |
| row           | document       |   数据记录行/文档 |
| column        | index          |   数据记录行/文档 |
| table joins   | -              |   表连接,MongoDB不支持 |
| primary key   | primary key 主键 |  MongoDB自动将_id字段设置为主键 |
## 定义Schema
mongoose中任何任何事物都是从Schema开始的。每一个Schema对应MongoDB中的一个集合（collection）。Schema中定义了集合中文档（document）的样式。
```
var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var blogSchema = new Schema({  
    title:  String,  
    author: String,  
    body:   String,  
    comments: [{ body: String, date: Date }],  
    date: { type: Date, default: Date.now },  
    hidden: Boolean, 
    meta: {    votes: Number,    favs:  Number  }
});
```
如果之后想要在Schema中添加键，可以使用Schema#add方法。
## 创造一个model
为了使用schema定义，我们需要转换blogSchema为一个Model。使用
```
mongoose.model(modelName, schema)
var BlogModel = mongoose.model('Blog', blogSchema);// 开始吧！
```
## 实例方法
Model的实例是document。实例有很多内置的方法，我们也可以给实例自定义方法。
```
var animalSchema = new Schema({ 
    name: String, type: String    
});
animalSchema.methods.findSimilarTypes = function (cb) {
    return this.model('Animal').find({ type: this.type }, cb);
}
```
现在所有的动物实例有findSimilarTypes方法。
```
var AnimalModel = mongoose.model('Animal', animalSechema);
var dog = new AnimalModel({ type: 'dog' });
dog.findSimilarTypes(function (err, dogs) { 
    console.log(dogs); // woof
});
```
重写一个默认的实例方法可能会导致不期待的结果。
## Statics方法
给Model添加一个静态方法也是简单的。
```
animalSchema.statics.findByName = function (name, cb) {
	this.find({ name: new RegExp(name, 'i') }, cb);
}

var AnimalModel = mongoose.model('Animal', animalSchema);
AnimalModel.findByName('fido', function (err, animals) { 
	console.log(animals);
});
```
### methods和statics的区别
区别就是一个给Model添加方法（statics），
一个给实例添加方法（methods）。
## 索引
MongoDB支持二级索引，定义索引有两种方式

路径级别 schema级别
```
var animalSchema = new Schema({  
    name: String,  
    type: String,  
    tags: { type: [String], index: true } // field level
    });

animalSchema.index({ name: 1, type: -1 }); // schema level, 1是正序，-1是倒序
```
如果要建立复合索引的话，在schema级别建立是必要的。

索引或者复合索引能让搜索更加高效，默认索引就是主键索引ObjectId，属性名为_id。

数据库中主要的就是CRUD操作，建立索引可以提高查询速度。但是过多的索引会降低CUD操作。深度好文如下

http://www.cnblogs.com/huangxincheng/archive/2012/02/29/2372699.html
## 虚拟属性
Schema中如果定义了虚拟属性，那么该属性将不写入数据库。写入数据库的还是原来的属性。
```
// 定义一个schema

var personSchema = new Schema({  
    name: {  first: String,    last: String  }
});

// 编译
var Person = mongoose.model('Person', personSchema);// 创造实例

var bad = new Person({ 
    name: { first: 'Walter', last: 'White' }
});
```
我们将名字分成名字和姓，如果要得到全名，我们需要
```
console.log(bad.name.first + ' ' + bad.name.last); // Walter White
```
这样无疑是麻烦的，我们可以通过虚拟属性的getter来解决这个问题。
```
personSchema.virtual('name.full').get(function () { 
    return this.name.first + ' ' + this.name.last;
});
```
那么就可以使用bad.name.full直接调用全名了。

反之，如果我们知道虚拟属性name.full，通过setter也可以得到组成name.full的每一项。
```
personSchema.virtual('name.full').set(function (name) {  
    var split = name.split(' ');  
    this.name.first = split[0];  
    this.name.last = split[1];
});

mad.name.full = 'Breaking Bad';
console.log(mad.name.first); // Breaking
console.log(mad.name.last);  // Bad
```
## 配置项
schema有一些配置项可以使用，有两种方式：
```
new Schema({…}, options)

var schema = new Schema({...});
schema.set(option, value);
```
有效的配置有:
1. autoIndex（默认true）
2. capped
3. collection
4. id _id（默认true）
5. read safe（默认true）
6. shardKey strict（默认true）
7. toJSON
8. toObject
9. versionKey
10. typeKey
11. validateBeforeSave
12. skipVersioning
13. timestamps
14. useNestedStrict
15. retainKeyOrder

### autoIndex–自动索引
应用开始的时候，Mongoose对每一个索引发送一个ensureIndex的命令。索引默认（_id）被Mongoose创建。

当我们不需要设置索引的时候，就可以通过设置这个选项。
```
var schema = new Schema({..}, { autoIndex: false });
var Clock = mongoose.model('Clock', schema);
Clock.ensureIndexes(callback);
```
### bufferCommands
似乎是说这个（mongoose buffer）管理在mongoose连接关闭的时候重连，如果取消buffer设置，如下：（存疑）
```
var schema = new Schema({..}, { bufferCommands: false });
```
### capped–上限设置
如果有数据库的批量操作，该属性能限制一次操作的量，例如：
```
new Schema({...},{capped:1024});  //一次操作上线1024条数据
```
当然该参数也可是对象，包含size、max、autiIndexId属性
```
new Schema({...},{capped:{size:1024,max:100,autoIndexId:true}});
```
### collection–集合名字
在MongDB中默认使用Model的名字作为集合的名字，如过需要自定义集合的名字，可以通过设置这个选项。
```
var schema = new Schema({...}, {collection: 'yourName'});
```
### id
mongoose分配给每一个schema一个虚拟属性id，它是一个getter。返回的是_id转换为字符串后的值。如果不需要为schema添加这个getter，可以通过id配置修改。
```
// 默认行为
var pageSchema = new Schema({ name: String });
var pageModel = mongoose.model('Page', pageSchema);
var p = new pageModel({ name: 'mongodb.org' });
console.log(p.id); // '50341373e894ad16347efe01'

// 禁止id
var pageSchema = new Schema({ name: String }, { id: false } );
var pageModel = mongoose.model('Page', pageSchema);
var p = new pageModel({ name: 'mongodb.org' });
console.log(p.id); // undefined
```
### _id
在一个schema中如果没有定义_id域（field），那么mongoose将会默认分配一个_id域（field）。类型是ObjectId。如果不需要使用这个默认的选择，可以通过设置这个选项。

通过在schema中设置这个字段可以阻止生成mongoose获得_id。但是在插入的时候仍然会生成_id。设置这个字段之后，如果再使用Schema.set(’_id’, false)将无效。
```
// 默认行为
var pageSchema = new Schema({ name: String });
var pageModel = mongoose.model('Page', pageSchema);
var p = new pageModel({ name: 'mongodb.org' });
console.log(p); // { _id: '50341373e894ad16347efe01', name: 'mongodb.org' }

// 禁用 _id
var pageSchema = new Schema({ name: String }, { _id: false });
// schema构造器设置之后，不要再像下面这样设置
// var schema = new Schema({ name: String });
// schema.set('_id', false);

var PageModel = mongoose.model('Page', pageSchema);
var p = new pageModel({ name: 'mongodb.org' });
console.log(p); // { name: 'mongodb.org' }
// 当插入的时候，MongoDB将会创建_id
p.save(function (err) {  
    if (err) return handleError(err);  
    pageModel.findById(p, function (err, doc) { 
        if (err) return handleError(err);   
        console.log(doc); 
        // { name: 'mongodb.org', _id: '50341373e894ad16347efe12' }  
    })
})
```
为什么不建议使用set
### read
允许在schema级别设置query#read，对于所有的查询，提供给我们一种方法应用默认的ReadPreferences。
### safe
这个配置会在MongoDB所有的操作中起作用。如果设置成true就是在操作的时候要等待返回的MongoDB返回的结果，比如update，要返回影响的条数，才往后执行，如果safe：false，则表示不用等到结果就向后执行了。
默认设置为true能保证所有的错误能通过我们写的回调函数。我们也能设置其它的安全等级如：
```
{ j: 1, w: 2, wtimeout: 10000 }
```
表示如果10秒内写操作没有完成，将会超时。
关于j和w，这里有很好的解释。

http://kyfxbl.iteye.com/blog/1952941
### shardKey
需要mongodb做分布式，才会使用该属性。
### strict
默认是enabled，如果实例中的域（field）在schema中不存在，那么这个域不会被插入到数据库。
```
var ThingSchema = new Schema({a:String});
var ThingModel = db.model('Thing',SchemaSchema);
var thing = new Thing({iAmNotInTheThingSchema:true});
thing.save();//iAmNotInTheThingSchema这个属性将无法被存储

// 通过doc.set()设置也会受到影响。
var thingSchema = new Schema({..})
var Thing = mongoose.model('Thing', thingSchema);
var thing = new Thing;
thing.set('iAmNotInTheSchema', true);
thing.save(); // iAmNotInTheSchema is not saved to the db
```
如果取消严格选项，iAmNotInTheThingSchema将会被存入数据库
```
var thingSchema = new Schema({..}, { strict: false });
var thing = new Thing({ iAmNotInTheSchema: true });
thing.save(); // iAmNotInTheSchema is now saved to the db!!
```
该选项也可以在Model级别使用，通过设置第二个参数，例如：
```
var ThingModel = db.model('Thing');
var thing1 = new ThingModel(doc,true);  //启用严格
var thing2 = new ThingModel(doc,false); //禁用严格
```
strict也可以设置为throw，表示出现问题将会抛出错误而不是抛弃不合适的数据。

注意：
* 不要设置为false除非你有充分的理由。

在mongoose v2里默认是false。

在实例上设置的任何键值对如果再schema中不存在对应的，将会被忽视。
```
var thingSchema = new Schema({..})
var Thing = mongoose.model('Thing', thingSchema);
var thing = new Thing;
thing.iAmNotInTheSchema = true;
thing.save(); // iAmNotInTheSchema 不会保存到数据库。
```
### toJSON
和toObject类似，选择这个选项为true后，但是只有当实例调用了toJSON方法后，才会起作用。
```
var schema = new Schema({ name: String });
schema.path('name').get(function (v) { 
    return v + ' is my name';
});

schema.set('toJSON', { getters: true, virtuals: false });
var M = mongoose.model('Person', schema);
var m = new M({ name: 'Max Headroom' });
console.log(m.toObject()); // { _id: 504e0cd7dd992d9be2f20b6f, name: 'Max Headroom' }
console.log(m.toJSON()); // { _id: 504e0cd7dd992d9be2f20b6f, name: 'Max Headroom is my name' }
console.log(JSON.stringify(m)); // { "_id": "504e0cd7dd992d9be2f20b6f", "name": "Max Headroom is my name" }
```
可以看出，配置属性name对toObject没影响，对toJSON有影响。
### toObject
选择这个选项为true后，默认对这个schema所有的实例都有作用。不需要实例手动调用。
```
var schema = new Schema({ name: String });
schema.path('name').get(function (v) {  
    return v + ' is my name';
});

schema.set('toObject', { getters: true });
var M = mongoose.model('Person', schema);
var m = new M({ name: 'Max Headroom' });
console.log(m); // { _id: 504e0cd7dd992d9be2f20b6f, name: 'Max Headroom is my name' }
```
较上面不同的是，没有virtuals: false这个设置。
### typeKey
在mongoose里，如果schema里有个对象，并且这个对象有个type键，mongoose将会将这个作为一种类型声明。
```
// Mongoose 认为loc字段的类型是一个字符串，而不是有type这个字段 
var schema = new Schema({ loc: { type: String, coordinates: [Number] } });
```
然而，对于一些应用来说，type字段是必要的。那么可以通过typeKey来设置。
```
var schema = new Schema({ 
    // Mongoose 这时候认为loc字段有两个键，一个是type，一个是coordinates  
    loc: { type: String, coordinates: [Number] },  
    // Mongoose 这时候认为name字段的类型是字符串。  
    name: { $type: String }
},{ typeKey: '$type' }); // '$type'键意味着这是一个类型宣告，而不是默认的type
```
### validateBeforeSave
默认得，文档被保存到数据库的时候会自动验证，这是为了防止无效的文档。如果想要手动处理验证，并且能保存不通过验证的文档，可以设置这个选项为false。
```
var schema = new Schema({ name: String });
schema.set('validateBeforeSave', false);
schema.path('name').validate(function (value) {   
    return v != null;
});
var M = mongoose.model('Person', schema);
var m = new M({ name: null });
m.validate(function(err) { 
    console.log(err); // 将会告诉你null不被允许
});
m.save(); // 尽管数据无效，但是仍然可以保存。
```
### versionKey
版本锁设置在每一个文档（document）上，由mogoose生成。默认的值是__v，但是可以自定义。
```
var schema = new Schema({ name: 'string' });
var Thing = mongoose.model('Thing', schema);
var thing = new Thing({ name: 'mongoose v3' });
thing.save(); // { __v: 0, name: 'mongoose v3' }

// 自定义版本锁
new Schema({..}, { versionKey: '_somethingElse' });
var Thing = mongoose.model('Thing', schema);
var thing = new Thing({ name: 'mongoose v3' });
thing.save(); // { _somethingElse: 0, name: 'mongoose v3' }
```
不要将这个选项设置为false除非你知道你在做什么。
### skipVersioning
http://aaronheckmann.tumblr.com/post/48943525537/mongoose-v3-part-1-

按照这里的说法，大致是说，加入在一个博客系统中，一个人所有的评论是一个数组，那么所有的评论是有索引的，比如某一条评论的body，comments.3.body，这里3是索引。假如一个评论者（A）想要修改自己的评论，但是此时另一个评论者（B）删除（或其他操作）了自己的评论，那么对A的索引可能会造成变化，此时对A的操作会发生错误。

为了改变这个问题，mongoose v3添加了version key配置。无论什么时候修改一个数组潜在地改变数组元素位置，这个version key(__V)的值会加1。在where条件中也需要添加__v条件，如果能通过（数组索引没改变），就可以修改，例如：
```
posts.update(
    { _id: postId, __v: verionNumber } ,
    { $set: { 'comments.3.body': updatedText }}
);
```
如果在更新之前删除了评论，那么就会发生错误。
```
post.save(function (err) { 
    console.log(err); // Error: No matching document found.
});
```
### timestamps
如果在schema设置这个选项，createdAt和updatedAt域将会被自动添加的文档中。它们默认的类型是Date，默认的名字是createdAt和updatedAt，不过我们可以自己修改。
```
var thingSchema = new Schema({..}, { timestamps: { createdAt: 'created_at' } });
var Thing = mongoose.model('Thing', thingSchema);
var thing = new Thing();
thing.save(); // created_at & updatedAt将会被包含在文档。
```
### useNestedStrict
在mongoos 4， update()和findOneAndUpdate()方法只检查顶级schema的strict的选项设置。
```
var childSchema = new Schema({}, { strict: false });// 这里parentSchema是topSchema，而childSchema是subSchema。
var parentSchema = new Schema({ child: childSchema }, { strict: 'throw' });
var Parent = mongoose.model('Parent', parentSchema);
Parent.update({}, { 'child.name': 'Luke Skywalker' }, function(error) {  
    // 发生错误因为parentSchema设置了strict: 'throw'}
    // 即使childSchema设置了{strict: false}
});
var update = { 'child.name': 'Luke Skywalker' };
var opts = { strict: false };
Parent.update({}, update, opts, function(error) { 
    // 这个可以通过因为重写了parentSchema的strict选项
});
```
如果设置了useNestedStrict为true，mogoose在更新时使用childSchema的strict选项。
```
var childSchema = new Schema({}, { strict: false });
var parentSchema = new Schema({ child: childSchema },  { strict: 'throw', useNestedStrict: true });
var Parent = mongoose.model('Parent', parentSchema);
Parent.update({}, { 'child.name': 'Luke Skywalker' }, function(error) { 
    // 可以更新
});
```
### retainKeyOrder
默认得，mongoose会转换实体中键的顺序。比如
```
new Model({ first: 1, second: 2 })
```
将会在MongoDB中存储为{ second: 2, first: 1 }；这带来了极大的不方便。

Mongoose v4.6.4 有一个retainKeyOrder选项确保mongoose不会改变键的顺序。
>[原文出处](http://cnodejs.org/topic/58b911997872ea0864fee313)

>参考

>http://cnodejs.org/topic/504b4924e2b84515770103dd?utm_source=ourjs.com

>http://www.nodeclass.com/api/mongoose.html#schema_Schema-add

>http://mongoosejs.com/docs/guide.html