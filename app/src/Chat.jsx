import React from 'react';
import UserList from './UserList';

class Chat extends React.Component{


    componentDidMount() {
        const conn = new WebSocket('ws://localhost:8090?' + this.props.token);
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            console.log(e.data);
        };

    }

    render(){
        return (


            <div>
                <h1>{this.props.token}</h1>
                <div>
                    <UserList />
                </div>
            </div>


        )
    }
}

export default Chat;