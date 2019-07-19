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

        this.conn.onmessage = (e) => {
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

                    break;

                case 'updateOnlineList':
                    this.setState({
                        onlineList: message.onlineList

                    });

                    break;
                case 'updateUserList':
                    this.setState({
                        userList: message.userList

                    });

                    break;

                case 'admin':
                     this.setState({
                         admin: true

                     })
                    break

                default:
                    console.log('default worked');

            }

        };//.bind(this);

        this.conn.onclose= function(e){
            localStorage.clear();
        };

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
    exit= ()=>{
        this.conn.close();
        localStorage.clear();
        window.location.reload()
    }




    render(){
        return (


            <div className="container">
                <div className="row">
                    <div className="col">
                        <h1 onClick={this.exit}>{localStorage.getItem('username')}</h1>
                    </div>
                </div>
                <div className="row">
                    <div className="col-4">
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
                    </div>
                    <div className="col-auto">
                        <div className="row">

                            <div className="dinamicChat">
                                <ul>
                                    {this.state.messages.map((message,index) => <li key={index} style={{ background: '#ececec', color: message.color }}>
                                        {message.sent.substr(11, 12)} -- {message.author} : {message.content}
                                    </li>)}
                                </ul>
                            </div>
                        </div>
                        <div className="row">
                            <div className="input-group mb-3">
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Recipient's username"
                                    aria-label="Recipient's username"
                                    aria-describedby="button-addon2"
                                    onChange={this.handleChange}
                                    name="inputMessage"
                                    value={this.state.inputMessage}
                                ></input>
                                <div className="input-group-append">
                                    <button
                                        className="btn btn-outline-secondary"
                                        type="button"
                                        id="button-addon2"
                                        onClick={this.handleSend.bind(this)}
                                    >
                                        Send

                                    </button>
                                    </div>
                            </div>
                        </div>

                    </div>
                </div>



            </div>


        )
    }
}




export default Chat;

