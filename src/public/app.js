"use strict";

import { hideLoader as hideLoader, showLoader as showLoader } from "./js/loader.js";
import { buildQuestion, buildResults } from "./js/builder.js";


let container = document.getElementById('container');
let deleteButton = document.getElementById('delete-button');
let nextButton = document.getElementById('next-button');
let spinnerContainer = document.getElementById('spinner-container')

var loadedProgress = null;
let currentDetails = {};

const apiUrl = 'api/v1/';

async function getData(url) {
    const response = await fetch(url, {
        method: 'GET',
        cache: 'no-cache',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    });
    const responseData = await response.json()
    return responseData;
}

async function updateData(url, data) {

    const response = await fetch(url, {
        method: 'POST',
        cache: 'no-cache',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ progress: data })
    });

    const responseData = await response.json();
    return JSON.parse(responseData);
}

async function deleteData(url) {
    const response = await fetch(url, {
        method: 'DELETE',
        cache: 'no-cache',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    });
    return true;
}

async function getQuestion(id = '1') {
    const url = apiUrl + 'questions/' + id;
    let responseObject = await getData(url);
    return responseObject;
}

async function getAnswers() {
    const url = apiUrl + 'questions';
    let responseObject = await getData(url);
    return responseObject;

}

async function getProgress() {
    const url = apiUrl + 'saved';
    let responseObject = await getData(url);
    let parsedObject = JSON.parse(responseObject)
    return parsedObject == null ? null : parsedObject.progress;
}

async function resetProgress() {
    const url = apiUrl + 'saved';
    let responseObject = await deleteData(url);
    loadedProgress = null;
    currentDetails = {};
    nextQuestion();
}

async function updateProgress(data) {
    const url = apiUrl + 'saved';
    let responseObject = await updateData(url, data);

    return responseObject;
}


async function saveProgress() {
    let selection = container.querySelector('input:checked') == null;

    if (selection) {
        console.log('Nothing checked or in Results page');
        return false;
    }

    let value = selection ? null : container.querySelector('input:checked').value;
    if (loadedProgress == null) {
        loadedProgress = [];
    }
    loadedProgress[currentDetails.id] = value;

    if (await updateProgress(loadedProgress) == loadedProgress) {
        console.log('happened');
    }

    nextQuestion();
}

function clearTable() {
    container.innerHTML = '';
}

async function nextQuestion() {
    clearTable();
    showLoader(spinnerContainer);
    init();
}

async function showResults() {
    let correctAnswers = await getAnswers();
    let result = 0;
    let details = [];
    for (let i = 0; i < loadedProgress.length-1; i++) {
        let obj = {
            id: correctAnswers[i].id
        };
        if(loadedProgress[i+1] == correctAnswers[i].correct_answer){
            obj.correct = true;
            result += 1;
        } else {
            obj.correct = false;
        }
        details.push(obj);
    }
    details.result = result;
    hideLoader(spinnerContainer);
    container.append(buildResults(details));
}

const init = async function () {
    
    getProgress().then(async (progress) => {
        let data;
        if (progress === null) {
            data = await getQuestion(1);
        } else {
            loadedProgress = progress;
            data = await getQuestion(loadedProgress.length);
        }

        if (data.status == false) {
            showResults();
            return;
        }
        hideLoader(spinnerContainer);
        container.append(buildQuestion(data));
        currentDetails = data;
    });
}

window.addEventListener('DOMContentLoaded', () => {
    init();
});

nextButton.addEventListener('click', () => {
    saveProgress(); 
});

deleteButton.addEventListener('click', () => {
    resetProgress();
});
