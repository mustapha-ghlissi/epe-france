import React, {Component, Fragment} from 'react';

class Note extends Component {

    getRest = (note) => {
        let li = [];
        for (let i = 1; i <= note; i++) {
            li.push(
                <li><i className="fas fa-star text-warning"></i></li>
            );
        }

        if (note < 5) {
            for (let i = note + 1; i <= 5; i++) {
                li.push(
                    <li><i className="far fa-star text-warning"></i></li>
                );
            }
        }
        return li;
    }


    render() {

        const {note} = this.props;

        return (
            <ul className='list-unstyled p-0 m-0 mr-3 d-inline-flex align-items-center justify-content-center'>
                {
                    note ?
                        (
                            note > 0 && note < 1 ?
                                <Fragment>
                                    <li><i className="fas fa-star-half-alt text-warning"></i></li>
                                    <li><i className="far fa-star text-warning"></i></li>
                                    <li><i className="far fa-star text-warning"></i></li>
                                    <li><i className="far fa-star text-warning"></i></li>
                                    <li><i className="far fa-star text-warning"></i></li>
                                </Fragment>
                                : note > 1 && note < 1.5 ?
                                <Fragment>
                                    <li><i className="fas fa-star text-warning"></i></li>
                                    <li><i className="far fa-star text-warning"></i></li>
                                    <li><i className="far fa-star text-warning"></i></li>
                                    <li><i className="far fa-star text-warning"></i></li>
                                    <li><i className="far fa-star text-warning"></i></li>
                                </Fragment>
                                : note >= 1.5 && note < 2 ?
                                    <Fragment>
                                        <li><i className="fas fa-star text-warning"></i></li>
                                        <li><i className="fas fa-star-half-alt text-warning"></i></li>
                                        <li><i className="far fa-star text-warning"></i></li>
                                        <li><i className="far fa-star text-warning"></i></li>
                                        <li><i className="far fa-star text-warning"></i></li>
                                    </Fragment>
                                    : note > 2 && note < 2.5 ?
                                        <Fragment>
                                            <li><i className="fas fa-star text-warning"></i></li>
                                            <li><i className="fas fa-star text-warning"></i></li>
                                            <li><i className="far fa-star text-warning"></i></li>
                                            <li><i className="far fa-star text-warning"></i></li>
                                            <li><i className="far fa-star text-warning"></i></li>
                                        </Fragment>
                                        : note >= 2.5 && note < 3 ?
                                            <Fragment>
                                                <li><i className="fas fa-star text-warning"></i></li>
                                                <li><i className="fas fa-star text-warning"></i></li>
                                                <li><i className="fas fa-star-half-alt text-warning"></i></li>
                                                <li><i className="far fa-star text-warning"></i></li>
                                                <li><i className="far fa-star text-warning"></i></li>
                                            </Fragment>
                                            : note > 3 && note < 3.5 ?
                                                <Fragment>
                                                    <li><i className="fas fa-star text-warning"></i></li>
                                                    <li><i className="fas fa-star text-warning"></i></li>
                                                    <li><i className="fas fa-star text-warning"></i></li>
                                                    <li><i className="far fa-star text-warning"></i></li>
                                                    <li><i className="far fa-star text-warning"></i></li>
                                                </Fragment>
                                                : note >= 3.5 && note < 4 ?
                                                    <Fragment>
                                                        <li><i className="fas fa-star text-warning"></i></li>
                                                        <li><i className="fas fa-star text-warning"></i></li>
                                                        <li><i className="fas fa-star text-warning"></i></li>
                                                        <li><i className="fas fa-star-half-alt text-warning"></i></li>
                                                        <li><i className="far fa-star text-warning"></i></li>
                                                    </Fragment>
                                                    : note > 4 && note < 4.5 ?
                                                        <Fragment>
                                                            <li><i className="fas fa-star text-warning"></i></li>
                                                            <li><i className="fas fa-star text-warning"></i></li>
                                                            <li><i className="fas fa-star text-warning"></i></li>
                                                            <li><i className="fas fa-star text-warning"></i></li>
                                                            <li><i className="far fa-star text-warning"></i></li>
                                                        </Fragment>
                                                        : note >= 4.5 && note < 5 ?
                                                            <Fragment>
                                                                <li><i className="fas fa-star text-warning"></i></li>
                                                                <li><i className="fas fa-star text-warning"></i></li>
                                                                <li><i className="fas fa-star text-warning"></i></li>
                                                                <li><i className="fas fa-star text-warning"></i></li>
                                                                <li><i
                                                                    className="fas fa-star-half-alt text-warning"></i>
                                                                </li>
                                                            </Fragment>
                                                            :
                                                            this.getRest(note)
                        )
                        :
                        <Fragment>
                            <li><i className="far fa-star text-warning"></i></li>
                            <li><i className="far fa-star text-warning"></i></li>
                            <li><i className="far fa-star text-warning"></i></li>
                            <li><i className="far fa-star text-warning"></i></li>
                            <li><i className="far fa-star text-warning"></i></li>
                        </Fragment>
                }
            </ul>
        );
    }
}

export default Note;
