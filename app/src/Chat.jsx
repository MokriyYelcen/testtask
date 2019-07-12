import React from 'react';
import UserList from './UserList';

class Chat extends React.Component{
    conn ;
    constructor(props){
        super(props);
        this.conn=new WebSocket('ws://localhost:8090?' + this.props.token);
    }


    componentDidMount() {


        this.conn.onopen = function(e) {
            console.log("Connection established!");

        };

        this.conn.onmessage = function(e) {
            console.log(e.data);
        };

    }

    handleSend= function(e){
        const obj={
            type:"message",
            status:"input",
            content:"test content "+this.props.token
        };
        this.conn.send(JSON.stringify(obj));


    };

    render(){
        return (


            <div>
                <h1>{this.props.token}</h1>
                <button onClick={this.handleSend.bind(this)} >send</button>
                <div>
                    <UserList />
                </div>
            </div>


        )
    }
}

export default Chat;