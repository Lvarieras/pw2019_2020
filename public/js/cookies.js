function acceptCookie() {
    $.ajax({
        url: "/checkCookie",
        type: "POST",
        data: "accept=true",
        beforeSend: function () {
        },
        success: function (response) {
            console.log(response.visited);
            document.getElementById('cookie-opacity').style.display = 'none';
        },
        error: function (data) {
        }
    });    
}

function refuseCookie() {
    alert("Vous devez accepter de manger le cookie");
    document.getElementById('cookie-opacity').style.display = '';
}

function checkCookie() {
    $.ajax({
        url: "/checkCookie",
        type: "POST",
        data: "",
        beforeSend: function () {
        },
        success: function (response) {
            console.log(response.visited);
            if(response.visited == 0) {
                document.getElementById('cookie-opacity').style.display = '';
            }
        },
        error: function (response) {
        }
    });    
}

checkCookie();