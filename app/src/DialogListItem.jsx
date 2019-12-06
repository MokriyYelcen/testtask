import React from 'react';

class DialogListItem extends React.Component{
    constructor(props){
        super(props)

    }
    type;
    name;

    render(){
        return (
            <li
                className="list-group-item"
                key={this.props.index}
            >
                {this.props.name}
            </li>
        )
    }
}

export default DialogListItem;