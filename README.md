# PHPWebService
An easy to use webservice to get datas from external source trough PHP.

This PHP WebService is designed to access datas stored on a server from a distant source.

How does it work ?

You prepare all your queries (here we are using MySQL but it can be adapted) and store them in a JSON file with the following structure : 

{
      "SelectTags":
      {
          "type":"select",
          "sql":"SELECT * FROM Tags",
          "param":[]
       },
      "InsertCategory": 
      {
          "type":"insert",
          "sql":"INSERT INTO Category (label) VALUES (:label)",
          "param":["label"]
      },
      "DeleteCategory":
      {
          "type":"delete",
          "sql":"DELETE FROM Categories WHERE ID = :ID",
          "param":["ID"]
      },
      "SelectConnection":
      {
          "type":"select",
          "sql":"SELECT * FROM Users WHERE email = :email AND pwd = pwd",
          "param":["email","pwd"]
      }
}

It's important that all fields are filled, even if there is no params to your queries, just let an empty array.

Once you've set all your queries, you can call them ! It's simple : 

Send a POST or GET HTTP Request to your server with the following fields :
  auth : String | the authentification key stored in the class param "auth".
  name : String |the name of the query you want.
  param : Associative array with key=>value | where key is the param you want to bind and value his value.
  
Server side : get the HTTP Request and treat it as follow :
   $data = json_decode($_POST["data"], true);
   $ws = new WebService($data);
   $ws->getWSResponse();
   echo(json_encode($ws->_response));
   
Don't forget to include the class file.

The query is treated by the class that generates a response (associative array again) with the following structure :
  error : Boolean | True if query is success False if failed.
  msg : String | Information about the error.
  data : Array / Associative Array | The result of the query.
  lastId : String | The value of the last Id inserted, in the case of an insert query.
  
You must need to make changes to the WebsService class to adapt it yto your own database class. 

I hope you will find this class usefull. Please let me know of any problems that you encounter. 

Thanks to Pierre BONNAMY who worked with me on this.
  
