
const API_URL = "api.php";

const form = document.getElementById("studentForm");
const list = document.getElementById("studentList");
const message = document.getElementById("message");
const loading = document.getElementById("loading");
const studentId = document.getElementById("studentId");

let students = [];

function showMessage(text, type = "success") {
    message.textContent = text;
    message.className = type;
}

function clearForm() {
    form.reset();
    studentId.value = "";
    document.getElementById("formTitle").textContent = "Add Student";
}

async function requestAPI(url, options = {}) {
    const response = await fetch(url, options);
    const data = await response.json();

    if (!response.ok || !data.success) {
        throw new Error(data.message || "Request failed.");
    }

    return data;
}

async function loadStudents() {
    loading.hidden = false;
    loading.textContent = "Loading student records...";
    list.replaceChildren();

    try {
        const result = await requestAPI(API_URL);
        students = result.data;
        renderStudents();
        loading.hidden = true;
    } catch (error) {
        loading.textContent = "Could not load records.";
        showMessage(error.message, "error");
    }
}

function renderStudents() {
    list.replaceChildren();

    if (students.length === 0) {
        const row = document.createElement("tr");
        const cell = document.createElement("td");
        cell.colSpan = 5;
        cell.textContent = "No student records found.";
        row.appendChild(cell);
        list.appendChild(row);
        return;
    }

    students.forEach(student => {
        const row = document.createElement("tr");

        [student.id, student.name, student.email, student.course]
            .forEach(value => {
                const cell = document.createElement("td");
                cell.textContent = value;
                row.appendChild(cell);
            });

        const actions = document.createElement("td");

        const editButton = document.createElement("button");
        editButton.textContent = "Edit";
        editButton.addEventListener("click", () => {
            studentId.value = student.id;
            document.getElementById("name").value = student.name;
            document.getElementById("email").value = student.email;
            document.getElementById("course").value = student.course;
            document.getElementById("formTitle").textContent = "Edit Student";
            showMessage("Editing student #" + student.id);
            form.scrollIntoView({ behavior: "smooth" });
        });

        const deleteButton = document.createElement("button");
        deleteButton.textContent = "Delete";
        deleteButton.addEventListener("click", async () => {
            if (!confirm("Delete " + student.name + "?")) return;

            try {
                const result = await requestAPI(API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        action: "delete",
                        id: Number(student.id)
                    })
                });

                showMessage(result.message);
                await loadStudents();
            } catch (error) {
                showMessage(error.message, "error");
            }
        });

        actions.append(editButton, deleteButton);
        row.appendChild(actions);
        list.appendChild(row);
    });
}

form.addEventListener("submit", async event => {
    event.preventDefault();

    const id = studentId.value;
    const payload = {
        action: id ? "update" : "add",
        name: document.getElementById("name").value.trim(),
        email: document.getElementById("email").value.trim(),
        course: document.getElementById("course").value.trim()
    };

    if (id) payload.id = Number(id);

    try {
        const result = await requestAPI(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        showMessage(result.message);
        clearForm();
        await loadStudents();
    } catch (error) {
        showMessage(error.message, "error");
    }
});

document.getElementById("cancelEdit").addEventListener("click", () => {
    clearForm();
    showMessage("Form cleared.");
});

document.getElementById("refresh").addEventListener("click", loadStudents);

loadStudents();
