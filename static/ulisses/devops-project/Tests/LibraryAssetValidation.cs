using System.Collections.Generic;
using System.Linq;
using NUnit.Framework;
using Ulisses.Core.Utilities.Editor;
using UnityEditor.PackageManager;

namespace Ulisses.Project.Tests {
    [TestFixture]
    internal sealed class LibraryAssetValidation : AssetValidationBase<LibraryAssetValidation.AssetSource> {
        internal sealed class AssetSource : AssetSourceBase {
            protected override string ValidateAssetsInDirectory => "Library/PackageCache";
            protected override IEnumerable<string> CheckCircularDependenciesForPackages => PackageInfo
                .GetAllRegisteredPackages()
                .Where(p => !p.isDirectDependency)
                .Select(p => p.name);

            protected override bool ShouldValidateAsset(string assetPath) {
                if (!assetPath.Contains("ulisses-spiele")) {
                    return false;
                }

                return base.ShouldValidateAsset(assetPath);
            }
        }
    }
}