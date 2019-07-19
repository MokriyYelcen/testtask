import React from 'react';

class OnlineList extends React.Component{
    constructor(props){
        super(props)
    }

    render(){
            return (
                <ol className="list-group">
                    {this.props.onlineList.map((user,index) =>
                        <li
                        key={index}
                        className="list-group-item"
                    >
                        {user}
                    </li>)}
                </ol>
            )
    }
}

export default OnlineList;


