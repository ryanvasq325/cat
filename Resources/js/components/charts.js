import Requests from './requests.js';

const Charts = (() => {
  let _id = null;
  let _url = null;
  let _type = null;

  const request = new Requests();

  function setId(id) {
    _id = id;
    _url = null;
    _type = null;
    return api;
  }

  function getData(url) {
    _url = url;
    return api;
  }

})();