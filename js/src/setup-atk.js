import $ from 'external/jquery';

/* istanbul ignore next */
if ($.atk !== undefined) {
    throw new Error('Unexpected jQuery.atk property state');
}

const atk = {};
$.atk = atk;

export default atk;
