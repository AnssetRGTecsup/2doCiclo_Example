using System.Collections;
using System.Net;
using System.Text;
using TMPro;
using UnityEngine;
using UnityEngine.Networking;
using UnityEngine.UI;

public class ConnectionManager : MonoBehaviour
{
    private readonly string SERVER_URL = "localhost:80/TF/";
    private readonly string CONNECTION_URL = "Utils/Connection.php";
    private readonly string DROP_URL = "Utils/DropDatabase.php";
    private readonly string CREATE_URL = "Utils/CreateData.php";

    [SerializeField] private TMP_Text connectionText;
    [SerializeField] private TMP_Text databaseText;
    [SerializeField] private Button connectBtn;
    [SerializeField] private string dbName;

    [SerializeField] private bool _hasConnected;

    private string currentDatabase;

    public void TryConnection()
    {
        StartCoroutine(ConnectToServer());
    }

    public void TryDropConnection()
    {
        StartCoroutine (DropConnection());
    }

    public void TryCreateData()
    {
        StartCoroutine(CreateDataFIles());
    }

    private void HasNoConnectedSuccessfully()
    {
        connectionText.text = "Not Connected";
        databaseText.text = "";
    }

    private void HasConnectedSuccessfully()
    {
        connectionText.text = "Connected";
        databaseText.text = currentDatabase;
    }

    private IEnumerator ConnectToServer()
    {
        connectBtn.enabled = false;

        string CONNECT_USER_PHP = $"{SERVER_URL}/{CONNECTION_URL}";

        DB_Post new_Post_DB = new DB_Post(dbName);

        string jsonData = JsonUtility.ToJson(new_Post_DB);
        byte[] jsonToSend = Encoding.UTF8.GetBytes(jsonData);

        UnityWebRequest request = new UnityWebRequest(CONNECT_USER_PHP, "POST");
        request.uploadHandler = new UploadHandlerRaw(jsonToSend);
        request.downloadHandler = new DownloadHandlerBuffer();
        request.SetRequestHeader("Content-Type", "application/json");

        yield return request.SendWebRequest();

        if (request.result == UnityWebRequest.Result.ConnectionError ||
                request.result == UnityWebRequest.Result.ProtocolError)
        {
            Debug.LogError("Error de Red (Unity): " + request.error);
        }

        Debug.Log(request.downloadHandler.text);

        string jsonResult = request.downloadHandler.text;
        ServerResponse response = JsonUtility.FromJson<ServerResponse>(jsonResult);

        if (response.success)
        {
            Debug.Log("<color=green>Connected to Server</color> " + response.message);

            currentDatabase = response.db_name;

            HasConnectedSuccessfully();

            _hasConnected = true;

            
        }
        else
        {
            Debug.LogError("<color=red>ERROR PHP:</color> " + response.message);

            HasNoConnectedSuccessfully();

            _hasConnected = false;
        }

        connectBtn.enabled = true;
    }

    private IEnumerator DropConnection()
    {
        connectBtn.enabled = false;

        string DROP_DATABASE_PHP = $"{SERVER_URL}/{DROP_URL}";

        DB_Post new_Post_DB = new DB_Post(dbName);

        string jsonData = JsonUtility.ToJson(new_Post_DB);
        byte[] jsonToSend = Encoding.UTF8.GetBytes(jsonData);

        UnityWebRequest request = new UnityWebRequest(DROP_DATABASE_PHP, "POST");
        request.uploadHandler = new UploadHandlerRaw(jsonToSend);
        request.downloadHandler = new DownloadHandlerBuffer();
        request.SetRequestHeader("Content-Type", "application/json");

        yield return request.SendWebRequest();

        if (request.result == UnityWebRequest.Result.ConnectionError ||
                request.result == UnityWebRequest.Result.ProtocolError)
        {
            Debug.LogError("Error de Red (Unity): " + request.error);
        }

        Debug.Log(request.downloadHandler.text);

        string jsonResult = request.downloadHandler.text;
        ServerResponse response = JsonUtility.FromJson<ServerResponse>(jsonResult);

        if (response.success)
        {
            Debug.Log("<color=green>Drop Successfull: </color> " + response.message);

            HasNoConnectedSuccessfully();

            currentDatabase = "";

            _hasConnected = false;
        }
        else
        {
            Debug.LogError("<color=red>ERROR PHP:</color> " + response.message);

            HasConnectedSuccessfully();

            _hasConnected = true;
        }

        connectBtn.enabled = true;
    }

    private IEnumerator CreateDataFIles()
    {
        connectBtn.enabled = false;

        string CREATE_USER_PHP = $"{SERVER_URL}/{CREATE_URL}";

        DB_Post new_Post_DB = new DB_Post(dbName);

        string jsonData = JsonUtility.ToJson(new_Post_DB);
        byte[] jsonToSend = Encoding.UTF8.GetBytes(jsonData);

        UnityWebRequest request = new UnityWebRequest(CREATE_USER_PHP, "POST");
        request.uploadHandler = new UploadHandlerRaw(jsonToSend);
        request.downloadHandler = new DownloadHandlerBuffer();
        request.SetRequestHeader("Content-Type", "application/json");

        yield return request.SendWebRequest();

        if (request.result == UnityWebRequest.Result.ConnectionError ||
                request.result == UnityWebRequest.Result.ProtocolError)
        {
            Debug.LogError("Error de Red (Unity): " + request.error);
        }

        Debug.Log(request.downloadHandler.text);

        string jsonResult = request.downloadHandler.text;
        ServerResponse response = JsonUtility.FromJson<ServerResponse>(jsonResult);

        if (response.success)
        {
            Debug.Log("<color=green>Create Table</color> " + response.message);

            currentDatabase = response.db_name;

            HasConnectedSuccessfully();

            _hasConnected = true;


        }
        else
        {
            Debug.LogError("<color=red>ERROR PHP:</color> " + response.message);

            HasNoConnectedSuccessfully();

            _hasConnected = false;
        }

        connectBtn.enabled = true;
    }
}

[System.Serializable]
public class ServerResponse
{
    public bool success;
    public string message;
    public string db_name;

    public ServerResponse(bool success, string message, string db_name)
    {
        this.success = success;
        this.message = message;
        this.db_name = db_name; 
    }
}

[System.Serializable]
public class DB_Post
{
    public string nombre_bd;

    public DB_Post(string nombre_bd)
    {
        this.nombre_bd = nombre_bd;
    }
}