import React from 'react';

class UserList extends React.Component{
    constructor(props){
        super(props)


    }

    changeBanned=function(e){
        this.props.changeBanned(e.userid)
    }




    render(){
        if(this.props.userList){
            return (
                <ol>
                    {this.props.userList.map((user,index) => <li key={index}>
                        {user.username}
                        {!user.banned &&(<button userid={user.id} onClick={this.changeBanned.bind(this)}>ban</button>)}
                    </li>)}
                </ol>
            )

        }
        else{
            return (
                <ol>
                    {this.props.onlineList.map((user,index) => <li key={index}>
                        {user}
                    </li>)}
                </ol>
            )
        }

    }
}

export default UserList;