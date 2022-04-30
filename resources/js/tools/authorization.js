import axios from "axios";
import Cookie from "js-cookie";

export async function user()
{
    const accessInfo = {
        token: Cookie.get('_tn'),
        serial: Cookie.get('_sl'),
        job: Cookie.get('_jb')
    }

    let user = null;

    // if any of the access info is missing, return null
    if (!accessInfo.token || !accessInfo.serial || !accessInfo.job)
        return null;

    // send request to get user info
    await axios
          .get(`/accounts/me?token=${accessInfo.token}&serial=${accessInfo.serial}`, { baseURL: window.location.apiUrl })
          .then(res => {
              user = res.data.user;
              user.job = user.job.toString();
          })
          .catch(err => {
              // if response state code is 401, remove access info and return null
                if (err.response.status === 401)
                {
                    Cookie.remove('_tn', { domain: `.${window.location.hostname.replace('www.', '')}` });
                    Cookie.remove('_sl', { domain: `.${window.location.hostname.replace('www.', '')}` });
                    Cookie.remove('_jb', { domain: `.${window.location.hostname.replace('www.', '')}` });
                    user = null;
                }
                else
                    console.log(err);
          })

    if (user)
        if (accessInfo.job !== user.job)
            Cookie.set('_jb', user.job, {expires: 365*60, path: `.${window.location.hostname.replace('www.', '')}`});

    return user;
}

export default {
    user
}