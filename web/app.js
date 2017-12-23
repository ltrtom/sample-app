angular
    .module('sample',['ngMaterial'])
    .controller('sampleCtrl', function(userService, taskService, $mdToast) {

        const self = this;

        self.loadUsers = () => {
            userService.findAll().then(users => {
                this.users = users;
            });
        };

        self.addUser = () => {
            if (self.name && self.email) {
                userService.addUser(self.name, self.email).then(() => {
                    self.loadUsers();
                    self.name = self.email = '';
                    self.toast('Utilsateur ajouté');
                });
            }
        };

        self.deleteUser = (id) => {
            self.current = null;
            self.tasks = [];
            userService.deleteUser(id).then(() => {
                self.loadUsers();
                self.toast('Utilisateur supprimé');
            });
        };

        self.loadUserTask = (user) => {
            self.current = user;
            taskService.findAll(user.id).then(tasks => self.tasks = tasks);
        };

        self.addTask = () =>  {
            if (self.current && self.description) {
                taskService.addTask(self.current.id, self.description, self.status).then(() => {
                    self.loadUserTask(self.current);
                    self.description = '';
                });
            }
        };

        self.deleteTask = (id) => {
            if (self.current) {
                taskService.deleteTask(self.current.id, id).then(() => {
                    self.loadUserTask(self.current);
                    self.toast('Tâche supprimée');
                })
            }
        };

        self.toast = (message) => {
            $mdToast.show(
                $mdToast.simple()
                    .textContent(message)
                    .position('top right')
                    .hideDelay(2000)
            );
        };

        self.loadUsers();

    }).service('userService', function ($http) {

        this.findAll = () => $http.get('app.php/users').then(response => response.data);

        this.addUser = (name, email) => {

            return $http.post('app.php/users', {name, email}).then(response => response.data);
        };

        this.deleteUser = (id) => $http.delete(`app.php/users/${id}`).catch(err => {
            console.error(err);
        });

    }).service('taskService', function ($http) {

        this.findAll = (userId) => $http.get(`app.php/users/${userId}/tasks`).then(response => response.data);

        this.addTask = (userId, description, status) => {
            status = status ? 1 : 0;
            return $http.post(`app.php/users/${userId}/tasks`, {description, status}).then(response => response.data);
        };

        this.deleteTask = (userId, taskId) => $http.delete(`app.php/users/${userId}/tasks/${taskId}`).catch(err => {
            console.error(err);
        });
    });



