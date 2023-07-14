------------------------------------------
--- Insertion des donnees
-- Auteurs: Rayane ANNEN, Felix BREVAL et Alexis MARTINS
------------------------------------------

SET SEARCH_PATH TO ctf;

-------------------------
--- Utilisateurs
-------------------------

insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('root', 'root@root.root', 'toor', true, 'I am the root user', 'my-root.root');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('DigitalDive', 'digital@diva.com', '321', false, '', '');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('user', 'user@user.user', 'password', false, 'The default user', '');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('mhellicar3', 'bmuscroft3@skyrock.com', 'dnAsHq5', false, 'solution-oriented', 'https://scribd.com');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('rmccuish4', 'gforcer4@examiner.com', 'Kq0GB05dx', false, 'toolset', 'https://si.edu');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('lbridell5', 'dwaylen5@cbslocal.com', 'BdqewUzOjGH', false, 'project', 'http://disqus.com');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('dkennet8', 'nderill8@salon.com', '81bDaq', false, 'fault-tolerant', 'https://opensource.org');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('bvinker1', 'lungerecht1@fc2.com', 'KOaK1i8', false, 'archive', 'https://about.me');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('fkaspar0', 'tgilfoy0@prnewswire.com', '4vCxkuLb4', false, 'content-based', 'http://360.cn');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('Banana', 'banana@fruits.ch', 'fruits', true, 'I feel like a fruit', 'fruits.ch');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('igeorgins9', 'lcasely9@ifeng.com', 'mRtHdgmh', false, 'architecture', 'https://jiathis.com');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('wkiddy6', 'mscibsey6@acquirethisname.com', 'DFLLR9pM', false, 'challenge', 'http://home.pl');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('ebortolussi2', 'dhovee2@sohu.com', 'siGJvKuv6e', false, 'holistic', 'https://google.de');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('lhanbidge7', 'lchark7@gnu.org', 'SBvrLf0apm', false, 'contextually-based', 'https://storify.com');
insert into utilisateur (pseudo, adresseemail, motdepasse, estadministrateur, descriptionutilisateur, siteinternet) values ('CyberNinja', 'cyber@ninja.com', '123', false, '', '');

-------------------------
--- Type equipes
-------------------------
insert into type_equipe (nomtype) values ('Other');
insert into type_equipe (nomtype) values ('Pro');
insert into type_equipe (nomtype) values ('Student');

-------------------------
--- Equipes
-------------------------
insert into equipe (nom, motdepasse, typeequipe, crt_pseudo) values ('TheHackzors', 'password', 'Pro', 'CyberNinja');
insert into equipe (nom, motdepasse, typeequipe, crt_pseudo) values ('Rookies', 'r00kies', 'Student', 'user');

-------------------------
--- Membre equipes
-------------------------
insert into membre_equipe (equ_nom, uti_pseudo) values ('TheHackzors', 'DigitalDive');
insert into membre_equipe (equ_nom, uti_pseudo) values ('TheHackzors', 'Banana');
insert into membre_equipe (equ_nom, uti_pseudo) values ('Rookies', 'dkennet8');
insert into membre_equipe (equ_nom, uti_pseudo) values ('Rookies', 'bvinker1');
insert into membre_equipe (equ_nom, uti_pseudo) values ('Rookies', 'fkaspar0');

-------------------------
--- Salles
-------------------------
insert into salle (numerosalle, etage) values (23, 'C');
insert into salle (numerosalle, etage) values (23, 'B');
insert into salle (numerosalle, etage) values (1, 'F');
insert into salle (numerosalle, etage) values (4, 'K');

-------------------------
--- Serveurs
-------------------------
insert into serveur (adresselocale, emailmainteneur, numerosalle, etage) values ('127.0.0.1', 'LocalAdmin@ctfproject.com', 23, 'C');
insert into serveur (adresselocale, emailmainteneur, numerosalle, etage) values ('123.45.67.89', 'Root@ctfproject.com', 1, 'F');

-------------------------
--- Evenement
-------------------------
insert into evenement (nom, estenligne, datecreation, datefin, estjeopardy, crt_pseudo) values ('YverdHack', true, '2023-01-22', '2023-02-05', true, 'Banana');
insert into evenement (nom, estenligne, datecreation, datefin, estjeopardy, crt_pseudo) values ('HackMeIn', false, '2023-01-22', '2023-02-01', true, 'Banana');
insert into evenement (nom, estenligne, datecreation, datefin, estjeopardy, crt_pseudo) values ('PirHack the treasure', true, '2023-01-22', '2023-03-22', false, 'root');
insert into evenement (nom, estenligne, datecreation, datefin, estjeopardy, crt_pseudo) values ('Codegate', true, '2023-01-22', '2023-01-31', false, 'root');
insert into evenement (nom, estenligne, datecreation, datefin, estjeopardy, crt_pseudo) values ('HackCon', false, '2023-01-22', '2023-02-19', false, 'root');
insert into evenement (nom, estenligne, datecreation, datefin, estjeopardy, crt_pseudo) values ('FastHack', true, '2023-01-22', '2023-01-22', true, 'Banana');

-------------------------
--- Salle evenement
-------------------------
insert into salle_evenement (nosalle, etage, eve_id) values (23, 'C', 3);
insert into salle_evenement (nosalle, etage, eve_id) values (1, 'F', 3);

-------------------------
--- Challenge
-------------------------
insert into challenge (nom, eve_id) values ('Find the map', 3);
insert into challenge (nom, eve_id) values ('Find the treasure', 3);
insert into challenge (nom, eve_id) values ('Web Server Takeover', 4);
insert into challenge (nom, eve_id) values ('LFI/RFI', 4);
insert into challenge (nom, eve_id) values ('IntroductionChallenge', 5);
insert into challenge (nom, eve_id) values ('Restricted shell', 1);
insert into challenge (nom, eve_id) values ( 'Crypto challenge', 1);
insert into challenge (nom, eve_id) values ( 'Do you also like cryptowallets ?', 2);

-------------------------
--- Type Challenge
-------------------------
insert into type_challenge_jeopardy (nomtype) values ('Web');
insert into type_challenge_jeopardy (nomtype) values ('Forensic');
insert into type_challenge_jeopardy (nomtype) values ('Database');
insert into type_challenge_jeopardy (nomtype) values ('Reverse');
insert into type_challenge_jeopardy (nomtype) values ('Crypto');
insert into type_challenge_jeopardy (nomtype) values ('Network');
insert into type_challenge_jeopardy (nomtype) values ('Steganography');
insert into type_challenge_jeopardy (nomtype) values ('Programming');
insert into type_challenge_jeopardy (nomtype) values ('Misc');

-------------------------
--- Challenge jeopardy
-------------------------
insert into Challenge_jeopardy (challengeid, descriptionjeopardy, typejeopardy, auteur, datecreation, datefin) values (6, 'You must retrieve the forgotten password in the .passwd file.
<br/>
Your goal is to escalate the users one by one using the available commands.

<ul>
<li>host: chall.ctfproject.com</li>
<li>protocol: ssh</li>
<li>user: chall10</li>
<li>password: chall10</li>
</ul>', 'Misc', 'mccros', '2023-01-22', '2023-01-31');
insert into Challenge_jeopardy (challengeid, descriptionjeopardy, typejeopardy, auteur, datecreation, datefin) values (7, 'Learn more about cryptography if you want to solve this challenge !', 'Crypto', 'marcel', '2023-01-24', '2023-02-05');
insert into Challenge_jeopardy (challengeid, descriptionjeopardy, typejeopardy, auteur, datecreation, datefin) values (8, 'One simple step : steal all the ethereum !', 'Programming', 'Root', '2023-01-22', '2023-01-30');

-------------------------
--- Challenge attaque defense
-------------------------
insert into Challenge_Attaque_Defense (challengeid, flag, serveurid) values (1, 'M4P', 1);
insert into Challenge_Attaque_Defense (challengeid, flag, serveurid) values (2, 'TR34SUR3', 2);
insert into Challenge_Attaque_Defense (challengeid, flag, serveurid) values (3, 'weeeb', 2);
insert into Challenge_Attaque_Defense (challengeid, flag, serveurid) values (4, 'ExploitMe', 2);
insert into Challenge_Attaque_Defense (challengeid, flag, serveurid) values (5, 'ortni', 1);

-------------------------
--- Etapes
-------------------------
insert into etape (nom, descriptionetape, nbpoints, difficulte, flag, jeo_challengeid, date_creation) values ('Ciphertext Analysis', 'You have been provided with a ciphertext that has been encrypted using a complex algorithm. Your task is to determine the encryption algorithm and key used to encrypt the ciphertext.''
            ''Ciphertext: <pre class="text-white">V0d7ImNvbnRlbnQiOiJXZWxjb21lIHRvIHRoZSBjaHJvbWl1bSBjdGYhIEhlcmUncyB5b3VyIGZsYWc6IHtGTEFHfSIsImtleSI6IjEyMzQ1NiJ9Cg==</pre>', 100, 1, 'C1PHER', 7, '2023-01-22 14:55:47.481023');
insert into etape (nom, descriptionetape, nbpoints, difficulte, flag, jeo_challengeid, date_creation) values ('Key recovery', 'Using the encryption algorithm and key that you have determined in step 1, you will now need to recover the secret key that was used to encrypt the message.', 100, 1, 'recovered_key', 7, '2023-01-22 14:56:19.463817');
insert into etape (nom, descriptionetape, nbpoints, difficulte, flag, jeo_challengeid, date_creation) values ('Decryption', 'Using the secret key recovered in step 2, you will now need to decrypt the ciphertext and obtain the flag.', 200, 2, 'FL4G', 7, '2023-01-22 14:56:47.135967');
insert into etape (nom, descriptionetape, nbpoints, difficulte, flag, jeo_challengeid, date_creation) values ('Cryptographic Hash', 'The flag obtained in step 3 is not in its final form. You will now need to determine the cryptographic hash algorithm used and compute the final flag.', 200, 2, 'SHA-(-1)', 7, '2023-01-22 14:57:35.673637');
insert into etape (nom, descriptionetape, nbpoints, difficulte, flag, jeo_challengeid, date_creation) values ('Ethereum stealer', '''<p>The objective of this challenge is to find and exploit a vulnerability in the following smart contract deployed on the Ethereum blockchain. Your task is to steal as much Ether as possible from the contract''''s vault. If you succeed the flag will be displayed.</p>'' ||
        ''<pre class="text-white">
        pragma solidity ^0.8.0;
        contract CTFChallenge {
            address public owner;
            mapping(address => uint) public balances;
            uint public totalEther;''
            ''constructor() public {
                owner = msg.sender;
            }

            function deposit() public payable {
                require(msg.value > 0, "Deposit amount must be greater than zero");
                balances[msg.sender] += msg.value;
                totalEther += msg.value;
            }

            function withdraw() public {
                require(msg.sender == owner, "Only owner can withdraw");
                require(totalEther > 0, "Vault is empty");
                msg.sender.transfer(totalEther);
                totalEther = 0;
            }
        }
        </pre>''', 500, 5, 'Wh3r3 4r3 u?', 8, '2023-01-22 15:11:58.555413');
insert into etape (nom, descriptionetape, nbpoints, difficulte, flag, jeo_challengeid, date_creation) values ('awk', 'Using the available commands find the flag in flag.txt file in the directory of the next user: chall10-user3', 162, 2, 'fl4g', 6, '2023-01-22 14:41:55.724369');
insert into etape (nom, descriptionetape, nbpoints, difficulte, flag, jeo_challengeid, date_creation) values ('python', 'Using the available commands find the flag in flag.txt file in the directory of the next user: chall10-user2', 81, 1, 'fl4g', 6, '2023-01-22 14:41:39.046234');
insert into etape (nom, descriptionetape, nbpoints, difficulte, flag, jeo_challengeid, date_creation) values ('rbash', 'Using the available commands find the flag in .passwd file in the directory of the next user: chall10-user4', 450, 5, 'fl4g', 6, '2023-01-22 14:44:10.626853');

-------------------------
--- Equipe etape
-------------------------
insert into equipe_etape (eta_nom, eta_jeo_challengeid, equ_nom, date_realisation) values ('python', 6, 'Rookies', '2023-01-22 15:34:42.300788');
insert into equipe_etape (eta_nom, eta_jeo_challengeid, equ_nom, date_realisation) values ('awk', 6, 'Rookies', '2023-01-22 15:34:53.964383');
insert into equipe_etape (eta_nom, eta_jeo_challengeid, equ_nom, date_realisation) values ('awk', 6, 'TheHackzors', '2023-01-22 15:37:15.868426');
insert into equipe_etape (eta_nom, eta_jeo_challengeid, equ_nom, date_realisation) values ('python', 6, 'TheHackzors', '2023-01-22 15:46:32.515573');
insert into equipe_etape (eta_nom, eta_jeo_challengeid, equ_nom, date_realisation) values ('rbash', 6, 'TheHackzors', '2023-01-22 15:46:37.122388');

-------------------------
--- Evenement equipe
-------------------------
insert into evenement_equipe (eve_id, nom) values (3, 'Rookies');
insert into evenement_equipe (eve_id, nom) values (4, 'Rookies');
insert into evenement_equipe (eve_id, nom) values (1, 'Rookies');
insert into evenement_equipe (eve_id, nom) values (1, 'TheHackzors');

-------------------------
--- Equipe ChallengeAttDef
-------------------------
insert into Equipe_ChallengeAttaqueDefense (equ_nom, attdef_challengeid) values ('Rookies', 1);
insert into Equipe_ChallengeAttaqueDefense (equ_nom, attdef_challengeid) values ('Rookies', 2);
insert into Equipe_ChallengeAttaqueDefense (equ_nom, attdef_challengeid) values ('Rookies', 3);

-------------------------
--- Writeup
-------------------------
insert into writeup (titre, contenu, pseudo, challengeid) values ('Awk step', 'This writeup is about the challenge : restricted shell

## First step

Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum

```c
#include <stdio.h>

#define WIDTH 800
#define HEIGHT 600

int main() {
    int image[HEIGHT][WIDTH];

    // Create grayscale image
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            int gray = (x + y) % 256;
            image[y][x] = gray;
        }
    }

    // Save image to file
    FILE *fp = fopen("image.pgm", "w");
    fprintf(fp, "P2\n%d %d\n255\n", WIDTH, HEIGHT);
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            fprintf(fp, "%d ", image[y][x]);
        }
        fprintf(fp, "\n");
    }
    fclose(fp);

    return 0;
}
```', 'CyberNinja', 6);
insert into writeup (titre, contenu, pseudo, challengeid) values ('Python step', 'This writeup concerns the step "Python"

## Introduction
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam efficitur ante a varius pretium. Ut diam dolor, porttitor at ipsum ut, sollicitudin efficitur diam. Etiam sed iaculis lorem. Sed eget suscipit mi. Curabitur odio nisl, efficitur sed elementum sed, tempus a lorem. Sed ac viverra nunc. Nulla vel pulvinar augue. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Integer eros mi, imperdiet id commodo faucibus, venenatis ut eros. Suspendisse eget laoreet massa. Morbi scelerisque convallis vestibulum. Quisque dapibus risus eget dui venenatis tempus. In maximus velit diam, sed sodales urna sollicitudin et. Proin at turpis sed turpis gravida sagittis. Suspendisse accumsan gravida libero, sed faucibus velit rutrum at.

```python
import random

def hangman():
    word_list = ["python", "java", "javascript", "csharp", "swift"]
    word = random.choice(word_list)
    word = word.upper()
    word_letters = set(word)
    alphabet = set("ABCDEFGHIJKLMNOPQRSTUVWXYZ")
    used_letters = set()
    word_guessed = set()
    tries = 6
    print("Welcome to Hangman!")
    print("You have", tries, "tries to guess the word.")
    print(" ".join(word_guessed))
    while (len(word_letters) > 0) and tries > 0:
        print("Used letters: ", " ".join(used_letters))
        guess = input("Please enter a letter: ").upper()
        if guess in alphabet - used_letters:
            used_letters.add(guess)
            if guess in word_letters:
                word_guessed.add(guess)
                word_letters.discard(guess)
            else:
                tries -= 1
                print("Incorrect. You have", tries, "tries left.")
        else:
            print("You''ve already used that letter. Please try again.")
        print(" ".join(word_guessed))
    if tries == 0:
        print("You lost! The word was", word)
    else:
        print("Congratulations! You guessed the word", word)

hangman()

```', 'DigitalDive', 6);

