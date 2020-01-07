import React from 'react';
import DialogListItem from "./DialogListItem";



class MyDialogs extends React.Component{
    constructor(props){
        super(props)
    }

    render(){
        return (
            <ol className="list-group">
                <li className="list-group-item">
                    general
                </li>
                {this.props.dialogsList.map((dialog,index) =>
                    <DialogListItem
                        key={index}
                        name = {dialog.dialogName}
                        lastMessageText = {dialog.lastMessageText}
                        dialogType = {dialog.dialogType}
                    />
                    )}
            </ol>
        )
    }
}

export default MyDialogs;