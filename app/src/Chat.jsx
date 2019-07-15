import React from 'react';
import UserList from './UserList';

class Chat extends React.Component{
    conn ;
    constructor(props){
        super(props);
        this.conn=new WebSocket('ws://localhost:8090?' + this.props.token);
        this.state={
            onlineList:[],
            messages:[],
            inputMessage:''
        }
    }


    componentDidMount() {


        this.conn.onopen = function(e) {
            console.log("Connection established!");

        };

        this.conn.onmessage = function(e) {
            const message=JSON.parse(e.data);

            switch(message.type){
                case'oldMessages':
                    this.setState({
                        messages: message.messages

                    })
                    break
                case 'message':
                    if(message.status==='output'){
                        this.setState({
                            messages: [ ...this.state.messages,  message]
                        })

                    }
                    break

                case 'updateOnlineList':
                    this.setState({
                        onlineList: message.onlineList

                    })
                    break
                case 'admin':
                    //console.log(message.userList);
                     this.setState({
                         userList: message.userList

                     })
                    break

                default:
                    console.log('default worked');



            }

        }.bind(this);

    }

    handleChange = (e) => {
        const {name, value} = e.target;
        this.setState({[name]: value});
    }

    handleSend= function(e){
        const obj={
            type:"message",
            status:"input",
            content:this.state.inputMessage
        };
        this.conn.send(JSON.stringify(obj));


    };

    changeMutedStatus= (userId) =>{
        const obj={
            type:"chengeMuted",
            user:userId
        };
        this.conn.send(JSON.stringify(obj))
    }
    changeBannedStatus= (userId) =>{
        const obj={
            type:"chengeBanned",
            user:userId
        };
        this.conn.send(JSON.stringify(obj))
    }



    render(){
        return (


            <div>
                { this.state.userList && (
                    <Admin list={this.state.userList} />
                ) }


                <div className="onlineList">
                    <UserList onlineList={this.state.onlineList}
                              userList={this.state.userList}
                              chanheMuted={this.changeMutedStatus}
                              changeBanned={this.changeBannedStatus}
                    />
                </div>
                <div className="dinamicChat">
                    <ul>
                        {this.state.messages.map((message,index) => <li key={index}>
                            {message.sent.substr(11, 12)} -- {message.author} : {message.content}
                        </li>)}
                    </ul>
                </div>

                <h1>{this.props.username}</h1>
                <button
                    onClick={this.handleSend.bind(this)} >send</button>
                <textarea
                    onChange={this.handleChange}
                    name="inputMessage"
                    value={this.state.inputMessage}
                >

                </textarea>

            </div>


        )
    }
}

function Admin(props){

   return (
       <button>Admin</button>
   )
}


export default Chat;