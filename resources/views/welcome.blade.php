<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Laravel Api Crud</title>
    <style>
        .container{
            padding-top: 50px;
        }
        .remove{
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h4>Post</h4>
                <span id="successmsg"></span>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Name</td>
                            <td>Description</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h4>Create Posts</h4>
                <span id="successmsg"></span>
                <form name="myForm">
                    <div class="form-group">
                        <label for="">Title</label>
                        <input type="text" class="form-control" name="title">
                        <span id="errTitle"></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Description</label>
                        <textarea name="description" id="" class="form-control" rows="4"></textarea>
                        <span id="errDesc"></span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <!-- modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Post</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="editForm" id="editModal">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Title</label>
                        <input type="text" class="form-control" name="title">
                        <span id="errTitle"></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Description</label>
                        <textarea name="description" id="" class="form-control" rows="4"></textarea>
                        <span id="errDesc"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-block">Update</button>
                    </div>    
                </div>
            </form>
            </div>
        </div>
    </div>
    
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    // read
        var tableBody = document.getElementById('tableBody');
        var titleList = document.getElementsByClassName('titleList');
        var descList = document.getElementsByClassName('descList');
        var idList = document.getElementsByClassName('idList');
        var btnList = document.getElementsByClassName('btnList');  
       axios.get('/api/posts')
            .then(response => {
               response.data.forEach(function(item){  
                        tableCreate(item);
                    });          
                })
                .catch(function (err) {
                    // handle error
                    console.log(err);
                })
    //createtitle
    var myForm = document.forms['myForm'];
    var titleInput = myForm['title'];
    var descriptionInput = myForm['description'];
    myForm.onsubmit = function(e){
        e.preventDefault();
        axios.post('api/posts',{
            title: titleInput.value,
            description: descriptionInput.value,
        })
        .then(response =>{
            if(response.data.msg == 'Created success'){
                alertMsg(response.data.msg);
                myForm.reset();   
                tableCreate(response.data[0]);
            }else{
                var titleErr = document.getElementById('errTitle');
                var DescErr = document.getElementById('errDesc');
                titleErr.innerHTML = titleInput.value == '' ? '<i class="text-danger">'+response.data.msg.title+'</i>' : '';
                DescErr.innerHTML = descriptionInput.value == '' ? '<i class="text-danger">'+response.data.msg.description+'</i>' : '';       
            }
            })
        .catch(err =>{
            console.log(err);
        });
    }
    //edit
    var editForm = document.forms['editForm'];
    var editTitle = editForm['title'];
    var editDescription = editForm['description']
    var updateId , oldTitle;
    function clickBtn(postId){
        updateId = postId;
        axios.get('api/posts/'+postId)
            .then(response =>{
                editTitle.value = response.data.title;
                editDescription.value = response.data.description;
                oldTitle = response.data.title;
                console.log(oldTitle);
            })
            .catch(err=>{
                console.log(err);
            })
    }
    //update
    editForm.onsubmit = function (event){
        event.preventDefault();
        axios.put('/api/posts/'+updateId,{
                    title: editTitle.value,
                    description: editDescription.value,
                })
            .then(response => {
                alertMsg(response.data.msg);
                $('#editModal').hide();
                for (let i = 0; i < titleList.length; i++) {
                    if(titleList[i].innerHTML == oldTitle){
                        titleList[i].innerHTML = editTitle.value;
                        descList[i].innerHTML = editDescription.value;
                    }
                    
                }
            })
            .catch(err=>{
                console.log(err);
            });
    }
    //delete
    function clickDelete(postId){
        axios.delete('api/posts/'+postId)
            .then(response =>{ 
                    console.log(response.data.deleteId);
                    alertMsg(response.data.msg);
                   for (let i = 0; i < titleList.length; i++) {
                     if(titleList[i].innerHTML == response.data.deleteId.title){
                        titleList[i].style.display = idList[i].style.display = descList[i].style.display = btnList[i].style.display ='none';

                     }
                       
                   }
                })
            .catch(err=>{
                console.log(err);
            })
    }
    function tableCreate(item){
        tableBody.innerHTML +=
        '<tr>'+
            '<td class="idList">'+ item.id+'</td>' +
            '<td class="titleList">'+ item.title+'</td>' +
            '<td class="descList">'+ item.description+'</td>' +
            '<td class="btnList"><button class="btn btn-sm btn-success m-2" onclick="clickBtn('+ item.id +')" data-toggle="modal" data-target="#editModal">edit</button><button class="btn btn-sm btn-danger" onclick="clickDelete('+ item.id +')">delete</button></td>' +
        '</tr>';  
    }
    function alertMsg(msg){
        console.log(msg);
        document.getElementById('successmsg').innerHTML ='<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>'+msg+'</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button><div>';         
    }
    </script>
</body>
</html>