import React from 'react';

class Admin extends React.Component{
    constructor(props){
        super(props)
        this.state={
            showing:false
        }
    }

    changeBanned=function(e){
        //console.log(e.target.getAttribute('userid'))
        this.props.changeBanned(e.target.getAttribute('userid'))
    }

    changeMuted=function(e){
        //console.log(e.target.getAttribute('userid'))
        this.props.changeMuted(e.target.getAttribute('userid'))
    }

    showList=function(){
        switch(this.state.showing){

            case false:
                this.props.getUserList();
                this.setState({
                    showing:true
                });
                break
            case true:
                this.setState({
                    showing:false
                });
                break
        }
    }

    render(){
        return (
            <React.Fragment>
            <button
            onClick={this.showList.bind(this)}
        >{(this.state.showing && 'Hide admin list')||'Show admin list'}</button>

                {this.state.showing === true && (<ol
                    className="list-group"
                >
                    {this.props.userList.map((user,index) =>
                        <li
                        key={index}
                        className="list-group-item"

                        >
                        {user.username} -->
                        <button

                            className="btn btn-outline-danger"
                            userid={user.id}
                            onClick={this.changeBanned.bind(this)}
                        >
                            {(user.banned && "unban")||"ban"}
                        </button>-
                        <button
                            className="btn btn-outline-secondary"
                            userid={user.id}
                            onClick={this.changeMuted.bind(this)}
                        >
                            {(user.muted && "unmute")||"mute"}
                        </button>

                    </li>)}
                </ol>)}

            </React.Fragment>
                )
    }
}
export default Admin;