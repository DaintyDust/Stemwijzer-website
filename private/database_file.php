<?php
// Simple file-based database implementation
require_once 'databaseinfo.php';

if (!function_exists('getConnection')) {
    function getConnection()
    {
        // Return a dummy connection object for compatibility
        return new StdClass();
    }
}

if (!function_exists('initializeFileDatabase')) {
    function initializeFileDatabase()
    {
        global $db_path;
        $data_dir = dirname($db_path) . '/data/';

        // Create data directory if it doesn't exist
        if (!is_dir($data_dir)) {
            mkdir($data_dir, 0755, true);        }

        // Initialize with sample data if files don't exist
        if (!file_exists($data_dir . 'users.json')) {
            $users = [
                [
                    'id' => 1,
                    'naam' => 'admin',
                    'email' => 'admin@stemwijzer.nl',
                    'wachtwoord_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                    'profielfoto' => 'DefaultProfile.svg'
                ],
                [
                    'id' => 2,
                    'naam' => 'demo',
                    'email' => 'demo@stemwijzer.nl',
                    'wachtwoord_hash' => password_hash('demo123', PASSWORD_DEFAULT),
                    'profielfoto' => 'DefaultProfile.svg'
                ]
            ];
            file_put_contents($data_dir . 'users.json', json_encode($users, JSON_PRETTY_PRINT));
        }

        if (!file_exists($data_dir . 'parties.json')) {
            $parties = [
                [
                    'id' => 1,
                    'naam' => 'VVD',
                    'partij_leider' => 'Dilan Yeşilgöz',
                    'beschrijving' => 'De VVD staat voor vrijheid, verantwoordelijkheid en ondernemerschap.',
                    'afbeelding' => 'Geschiedenis-van-de-VVD.jpg'
                ],
                [
                    'id' => 2,
                    'naam' => 'PVV',
                    'partij_leider' => 'Geert Wilders',
                    'beschrijving' => 'De PVV zet zich in voor Nederland en de gewone Nederlander.',
                    'afbeelding' => 'pvv-300x219.jpg'
                ],
                [
                    'id' => 3,
                    'naam' => 'CDA',
                    'partij_leider' => 'Henri Bontenbal',
                    'beschrijving' => 'Het CDA verbindt mensen op basis van christelijke waarden.',
                    'afbeelding' => 'CDA-logo-groen-rgb.png'
                ],
                [
                    'id' => 4,
                    'naam' => 'D66',
                    'partij_leider' => 'Rob Jetten',
                    'beschrijving' => 'D66 staat voor een open, eerlijke en groene samenleving.',
                    'afbeelding' => 'rob-jetten-met-ruime-meerderheid-gekozen-tot-lijsttrekker-d66.jpg'
                ]
            ];
            file_put_contents($data_dir . 'parties.json', json_encode($parties, JSON_PRETTY_PRINT));
        }

        if (!file_exists($data_dir . 'news.json')) {
            $news = [
                [
                    'id' => 1,
                    'titel' => 'Verkiezingen 2025 aangekondigd',
                    'inhoud' => 'De nieuwe verkiezingen voor 2025 zijn officieel aangekondigd. Alle partijen bereiden zich voor op een intensieve campagne.',
                    'datum' => date('Y-m-d H:i:s'),
                    'auteur_id' => 1,
                    'afbeelding' => 'template-news.png'
                ],
                [
                    'id' => 2,
                    'titel' => 'Nieuwe stemwijzer gelanceerd',
                    'inhoud' => 'Het Neutraal Kieslab heeft een nieuwe, verbeterde stemwijzer gelanceerd om kiezers te helpen bij hun keuze.',
                    'datum' => date('Y-m-d H:i:s'),
                    'auteur_id' => 1,
                    'afbeelding' => 'logo-neutraal-kieslab-lichtblauw.svg'
                ]
            ];
            file_put_contents($data_dir . 'news.json', json_encode($news, JSON_PRETTY_PRINT));
        }

        return true;
    }
}

// File-based database functions
if (!function_exists('fileDb_select')) {
    function fileDb_select($table, $where = [])
    {
        global $db_path;
        $data_dir = dirname($db_path) . '/data/';
        $file = $data_dir . $table . '.json';

        if (!file_exists($file)) {
            return [];
        }

        $data = json_decode(file_get_contents($file), true);
        if (!$data) return [];

        // Apply where conditions
        if (!empty($where)) {
            $data = array_filter($data, function ($row) use ($where) {
                foreach ($where as $key => $value) {
                    if (!isset($row[$key]) || $row[$key] != $value) {
                        return false;
                    }
                }
                return true;
            });
        }

        return array_values($data);
    }
}

if (!function_exists('fileDb_insert')) {
    function fileDb_insert($table, $data)
    {
        global $db_path;
        $data_dir = dirname($db_path) . '/data/';
        $file = $data_dir . $table . '.json';

        $existing = [];
        if (file_exists($file)) {
            $existing = json_decode(file_get_contents($file), true) ?: [];
        }

        // Auto-increment ID
        $max_id = 0;
        foreach ($existing as $row) {
            if (isset($row['id']) && $row['id'] > $max_id) {
                $max_id = $row['id'];
            }
        }
        $data['id'] = $max_id + 1;

        $existing[] = $data;
        return file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT)) !== false;
    }
}

if (!function_exists('fileDb_update')) {
    function fileDb_update($table, $data, $where)
    {
        global $db_path;
        $data_dir = dirname($db_path) . '/data/';
        $file = $data_dir . $table . '.json';

        if (!file_exists($file)) {
            return false;
        }

        $existing = json_decode(file_get_contents($file), true);
        if (!$existing) return false;

        $updated = false;
        foreach ($existing as &$row) {
            $match = true;
            foreach ($where as $key => $value) {
                if (!isset($row[$key]) || $row[$key] != $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                foreach ($data as $key => $value) {
                    $row[$key] = $value;
                }
                $updated = true;
            }
        }

        if ($updated) {
            return file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT)) !== false;
        }

        return false;
    }
}

// Initialize the file database
initializeFileDatabase();

// Legacy function compatibility
if (!function_exists('GetAllParties')) {
    function GetAllParties()
    {
        return fileDb_select('parties');
    }
}

if (!function_exists('GetPartyByName')) {
    function GetPartyByName($name)
    {
        $parties = fileDb_select('parties', ['naam' => $name]);
        return !empty($parties) ? $parties : null;
    }
}

if (!function_exists('GetAllNews')) {
    function GetAllNews()
    {
        return fileDb_select('news');
    }
}

if (!function_exists('GetUserByUsername')) {
    function GetUserByUsername($username)
    {
        $users = fileDb_select('users', ['naam' => $username]);
        return !empty($users) ? $users[0] : null;
    }
}

if (!function_exists('GetUserByEmail')) {
    function GetUserByEmail($email)
    {
        $users = fileDb_select('users', ['email' => $email]);
        return !empty($users) ? $users[0] : null;
    }
}

if (!function_exists('CreateUser')) {
    function CreateUser($conn, $username, $email, $password)
    {
        $userData = [
            'naam' => $username,
            'email' => $email,
            'wachtwoord_hash' => password_hash($password, PASSWORD_DEFAULT),
            'profielfoto' => 'DefaultProfile.svg'
        ];
        
        return fileDb_insert('users', $userData) ? $userData['id'] : null;
    }
}

if (!function_exists('VerifyUser')) {
    function VerifyUser($username, $password)
    {
        $user = GetUserByUsername($username);
        if ($user && password_verify($password, $user['wachtwoord_hash'])) {
            return $user;
        }
        return null;
    }
}

if (!function_exists('UpdateUserProfilePicture')) {
    function UpdateUserProfilePicture($userId, $filename)
    {
        return fileDb_update('users', ['profielfoto' => $filename], ['id' => $userId]);
    }
}

if (!function_exists('UpdateUsername')) {
    function UpdateUsername($userId, $newUsername)
    {
        return fileDb_update('users', ['naam' => $newUsername], ['id' => $userId]);
    }
}

if (!function_exists('UpdateUserEmail')) {
    function UpdateUserEmail($userId, $newEmail)
    {
        return fileDb_update('users', ['email' => $newEmail], ['id' => $userId]);
    }
}

if (!function_exists('UpdateUserPassword')) {
    function UpdateUserPassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return fileDb_update('users', ['wachtwoord_hash' => $hashedPassword], ['id' => $userId]);
    }
}

if (!function_exists('authenticateUser')) {
    function authenticateUser($conn, $username, $password)
    {
        // Try to find user by username
        $user = GetUserByUsername($username);
        
        // If not found by username, try by email
        if (!$user) {
            $user = GetUserByEmail($username);
        }
        
        if ($user && password_verify($password, $user['wachtwoord_hash'])) {
            return $user;
        }
        
        return false;
    }
}
