import React from 'react';
import OnlineList from './OnlineList';
import Admin from './Admin';

class Chat extends React.Component{
    conn ;
    constructor(props){
        super(props);
        this.conn=new WebSocket('ws://localhost:8090?' + this.props.token);
        this.state={
            onlineList:[],
            messages:[],
            inputMessage:'',
            admin:false,
            userList: []
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
                    console.log('oldMessages');
                    this.setState({
                        messages: message.messages

                    })
                    break
                case 'message':

                        this.setState({
                            messages: [ ...this.state.messages,  message]
                        })


                    break

                case 'updateOnlineList':

                    this.setState({
                        onlineList: message.onlineList

                    })
                    break
                case 'updateUserList':

                    this.setState({
                        userList: message.userList

                    })
                    break
                case 'admin':
                     this.setState({
                         admin: true

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
            content:this.state.inputMessage
        };
        this.conn.send(JSON.stringify(obj));


    };

    changeMutedStatus= (userId) =>{
        const obj={
            type:"changeMuted",
            user:userId
        };
        this.conn.send(JSON.stringify(obj))
    }
    changeBannedStatus= (userId) =>{
        const obj={
            type:"changeBanned",
            user:userId
        };
        this.conn.send(JSON.stringify(obj))
    }
    getUserList= () =>{
        const obj={
            type:"getUserList",
        };
        this.conn.send(JSON.stringify(obj))
    }



    render(){
        return (


            <div>
                { this.state.admin && (
                    <Admin
                        userList={this.state.userList}
                        getUserList={this.getUserList}
                        changeMuted={this.changeMutedStatus}
                        changeBanned={this.changeBannedStatus}
                    />
                ) }


                <div className="onlineList">
                    <OnlineList onlineList={this.state.onlineList}/>
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




export default Chat;

