import React from 'react';

class OnlineList extends React.Component{
    constructor(props){
        super(props)
    }

    render(){
            return (
                <ol>
                    {this.props.onlineList.map((user,index) => <li key={index}>
                        {user}
                    </li>)}
                </ol>
            )
    }
}

export default OnlineList;


