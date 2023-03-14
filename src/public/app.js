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
        body: JSON.stringify(data)
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

async function getResults() {
    const url = apiUrl + 'saved/results';
    let responseObject = JSON.parse(await getData(url));
    console.log(responseObject);
    return responseObject;

}

async function getProgress() {
    const url = apiUrl + 'saved';
    let responseObject = await getData(url);
    return responseObject;
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
    let envelope = {
        questionId: currentDetails.id,
        answer: value
    };

    await updateProgress(envelope);
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
    let correctAnswers = await getResults();
    let details = [];
    for (let i = 1; i < correctAnswers.length; i++) {
        details.push(
            {
                correct: correctAnswers[i],
                id: i
            }
        );
    }
    details.result = correctAnswers.filter((value)=>value).length;
    hideLoader(spinnerContainer);
    container.append(buildResults(details));
}

const init = async function () {
    
    getProgress().then(async (progress) => {
        let data;
        if (progress === null) {
            data = await getQuestion(1);
        } else if(progress.lastPage) {
            showResults();
            return;
        } else {
            loadedProgress = progress;
            data = await getQuestion(loadedProgress.progress.questionId + 1);
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
